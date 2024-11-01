<?php

namespace WC_Integration_NettiX\Plugin;

use Exception;
use WC_Integration_NettiX\Storage\Product_Map_Directory;
use WC_Integration_NettiX\Storage\WC_Product_Directory;
use WC_Product;

class Job_Handler {
	// Constants for product add and/or update tasks
	public const MAX_PRODUCT_UPDATE_TASKS = 10;
	public const TRANSIENT_NAME_PRODUCT_UPDATE_TASKS_LOCK = 'nettix-wc-integration-product-update-task-lock';

	private array $product_cache = [];
	private Product_Map_Directory $product_map_directory;
	private WC_Product_Directory $wc_product_directory;

	public function __construct(
		?Product_Map_Directory $product_map_directory = null,
		?WC_Product_Directory $wc_product_directory = null
	) {
		$this->product_map_directory = $product_map_directory ?? new Product_Map_Directory();
		$this->wc_product_directory  = $wc_product_directory ?? new WC_Product_Directory();
	}

	/* Product handling starts here */

	public function start_product_handling(): void {
		if ( get_transient( self::TRANSIENT_NAME_PRODUCT_UPDATE_TASKS_LOCK ) ) {
			return;
		}

		set_transient( self::TRANSIENT_NAME_PRODUCT_UPDATE_TASKS_LOCK, true, 2700 );

		for ( $i = 1; $i < self::MAX_PRODUCT_UPDATE_TASKS + 1; $i ++ ) {
			$this->start_running_product_handling_task( $i );
			usleep( 20000 );
		}
	}

	private function start_running_product_handling_task( int $job_handler_id ): void {
		$nonce = wp_create_nonce( hash( 'sha256', 'product_handling' . $job_handler_id . AUTH_SALT ) );
		$args  = [
			'body'      => [
				'action'         => 'wc_integration_nettix_run_product_handling_task',
				'nonce'          => $nonce,
				'job_handler_id' => $job_handler_id,
			],
			'timeout'   => 20,
			'blocking'  => false,
			'cookies'   => [],
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		];
		wp_remote_post( get_site_url() . '/wp-admin/admin-ajax.php', $args );
	}

	public function run_product_handling_task(): void {
		session_write_close();

		$nonce          = sanitize_text_field( $_POST['nonce'] );
		$job_handler_id = filter_var( $_POST['job_handler_id'], FILTER_SANITIZE_NUMBER_INT );

		if ( ! wp_verify_nonce( $nonce, hash( 'sha256', 'product_handling' . $job_handler_id . AUTH_SALT ) ) ) {
			wp_die();
		}

		$map = $this->product_map_directory->get_product_map_to_handle_for_job_handler( $job_handler_id );
		if ( ! $map ) {
			$this->continue_or_end_running_product_handling_tasks( $job_handler_id );
			wp_die();
		}

		if ( ! $map_id = $map['id'] ) {
			$this->continue_or_end_running_product_handling_tasks( $job_handler_id );
			wp_die();
		}

		$product_created = $map['product_created'] ?? true;
		$product_updated = $map['product_updated'] ?? false;
		$remove_product  = $map['remove'] ?? false;

		if ( ! $product_created ) {
			$product = $this->wc_product_directory->create_product( $map );
			$this->product_map_directory->set_product_id_to_map( $map['nettix_id'], $product->get_id() );
			$product_images = $this->parse_images_from_ad_response( $map );
			$this->create_or_update_product_image_maps( $product, $product_images, $job_handler_id );
		} elseif ( ! $product_updated ) {
			try {
				$product = $this->wc_product_directory->update_product( $map );
				$this->product_map_directory->update_product_map(
					$map_id,
					$product->get_id(),
					$map['nettix_id'],
					$map['ad_data_hash'],
					$map['ad_data'],
					true,
					true,
					$job_handler_id
				);
			} catch ( Exception $e ) {
				error_log( 'NettiX API: update product failed for map ' . $map_id . " Error: " . $e->getMessage() );

				return;
			}

			$product_images = $this->parse_images_from_ad_response( $map );
			$this->create_or_update_product_image_maps( $product, $product_images, $job_handler_id );
		} elseif ( $remove_product ) {
			if ( ! $product = $this->wc_product_directory->get_product_by_id( $map['post_id'] ) ) {
				return;
			}

			$product_id = $product->get_id();
			$this->remove_images_from_media_library( array_merge( [ $product->get_image_id() ],
				$product->get_gallery_image_ids() ) );
			$product->delete( true );
			$this->product_map_directory->remove_product_image_maps( $product_id );
			$this->product_map_directory->remove_product_maps( $product_id );
		}

		$this->continue_or_end_running_product_handling_tasks( $job_handler_id );

		wp_die();
	}

	/**
	 * @param array $image_ids
	 *
	 * @return void
	 */
	private function remove_images_from_media_library( array $image_ids ): void {
		foreach ( $image_ids as $image_id ) {
			wp_delete_post( $image_id, true );
		}
	}

	/**
	 * Get product by ID using local product cache.
	 *
	 * @param int $product_id
	 *
	 * @return WC_Product|null
	 */
	private function get_wc_product( int $product_id ): ?WC_Product {
		if ( ! array_key_exists( $product_id, $this->product_cache ) ) {
			if ( ! $product = $this->wc_product_directory->get_product_by_id( $product_id ) ) {
				return null;
			}

			$this->product_cache[ $product_id ] = $product;
		}

		return $this->product_cache[ $product_id ];
	}

	/**
	 * @param int $job_handler_id
	 *
	 * @return void
	 */
	private function continue_or_end_running_product_handling_tasks( int $job_handler_id ): void {
		$map_to_handle = $this->product_map_directory->get_product_map_to_handle_for_job_handler( $job_handler_id );

		if ( ! $map_to_handle ) {
			$this->start_running_product_image_handling_task( $job_handler_id );

			return;
		}

		$this->start_running_product_handling_task( $job_handler_id );
	}

	/* Product image handling starts here */

	/**
	 * Start asynchronous run of image uploading and attaching task.
	 *
	 * @param int $job_handler_id
	 *
	 * @return void
	 * @see run_product_image_handling_task
	 */
	private function start_running_product_image_handling_task( int $job_handler_id ): void {
		$nonce = wp_create_nonce( hash( 'sha256', 'product_image_handling' . $job_handler_id . AUTH_SALT ) );
		$args  = [
			'body'      => [
				'action'         => 'wc_integration_nettix_run_product_image_handling_task',
				'nonce'          => $nonce,
				'job_handler_id' => $job_handler_id,
			],
			'timeout'   => 20,
			'blocking'  => false,
			'cookies'   => [],
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		];
		wp_remote_post( get_site_url() . '/wp-admin/admin-ajax.php', $args );
	}

	/**
	 * Upload product image from URL and attach it to WC product. This is run asynchronously through AJAX.
	 *
	 * @return void
	 */
	public function run_product_image_handling_task(): void {
		session_write_close();

		$nonce          = sanitize_text_field( $_POST['nonce'] ?? '' );
		$job_handler_id = filter_var( $_POST['job_handler_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT );

		if ( ! wp_verify_nonce( $nonce, hash( 'sha256', 'product_image_handling' . $job_handler_id . AUTH_SALT ) ) ) {
			wp_die();
		}

		try {
			$image_map = $this->product_map_directory->get_product_image_map_to_handle_for_job_handler( $job_handler_id );
			if ( ! $image_map ) {
				$this->continue_or_end_running_product_image_handling_tasks( $job_handler_id );
				wp_die();
			}

			$image_map_id = $image_map['id'] ?? null;

			if ( ! $image_map_id ) {
				throw new Exception( "No image map ID, can't continue task" );
			}

			if ( $image_map['fetched'] && ! $image_map['remove'] ) {
				throw new Exception( "Product image map with ID $image_map_id nothing to do" );
			}

			if ( ! $product = $this->get_wc_product( $image_map['post_id'] ) ) {
				throw new Exception( "Could not find product with ID " . $image_map['post_id'] );
			}

			if ( $image_map['remove'] ) {
				$new_gallery_image_ids = [];
				foreach ( $product->get_gallery_image_ids() as $gallery_image_id ) {
					if ( $gallery_image_id != $image_map['image_id'] ) {
						$new_gallery_image_ids[] = $gallery_image_id;
					}
				}
				$product->set_gallery_image_ids( $new_gallery_image_ids );

				if ( $product->get_image_id() == $image_map['image_id'] ) {
					$new_main_image_id = $new_gallery_image_ids[0];
					$product->set_image_id( $new_main_image_id );
				}

				$product->save();

				$this->remove_images_from_media_library( [ $image_map['image_id'] ] );

				$this->product_map_directory->remove_product_image_map( $image_map['post_id'], $image_map['image_id'] );
			} else {
				$image_url = esc_url( sanitize_url( $image_map['image_url'] ), [ 'http', 'https' ], 'download' );

				$file = wc_rest_upload_image_from_url( $image_url );

				if ( is_wp_error( $file ) ) {
					throw new Exception( sprintf( "Image Upload Errors: %s",
						implode( ", ", $file->get_error_messages() ) ) );
				}

				if ( is_array( $file ) ) {
					$image_id = wc_rest_set_uploaded_image_as_attachment( $file, $product->get_id() );
					$this->product_map_directory->set_product_image_fetched( $image_map_id, $image_id );
					$this->update_product_images( $product );
				}
			}
		} catch ( Exception $e ) {
			error_log( 'NettiX API: ' . $e->getMessage() );
		}

		$this->continue_or_end_running_product_image_handling_tasks( $job_handler_id );

		wp_die();
	}

	private function update_product_images( WC_Product $product ): void {
		$image_maps = $this->product_map_directory->get_product_image_map_by_product_id( $product->get_id() );

		$image_ids = [];
		foreach ( $image_maps as $image_map ) {
			$image_ids[] = $image_map['image_id'];
		}

		if ( ! $product->get_image_id() ) {
			$product->set_image_id( array_shift( $image_ids ) );
		}

		$product->set_gallery_image_ids( $image_ids );
		$product->save();
	}

	private function continue_or_end_running_product_image_handling_tasks( $job_handler_id ): void {
		$image_map_to_handle = $this->product_map_directory->get_product_image_map_to_handle_for_job_handler( $job_handler_id );
		if ( ! $image_map_to_handle ) {
			$image_maps_to_handle = $this->product_map_directory->get_product_image_maps_to_handle( 10 );

			if ( count( $image_maps_to_handle ) == 0 ) {
				set_transient( self::TRANSIENT_NAME_PRODUCT_UPDATE_TASKS_LOCK, false );
			}

			return;
		}

		$this->start_running_product_image_handling_task( $job_handler_id );
	}

	private function create_or_update_product_image_maps(
		WC_Product $product,
		array $product_images,
		int $job_handler_id = 1
	): void {
		$product_id       = $product->get_id();
		$nettix_image_ids = [];

		foreach ( $product_images as $image ) {
			$nettix_image_ids[] = $image['nettix_image_id'];
			$product_image_map  = $this->product_map_directory->get_product_image_map_by_nettix_image_id( $image['nettix_image_id'] );

			// If image map wasn't found with NettiX image ID then try with NettiX ID and image filename for migration purposes
			if ( ! $product_image_map ) {
				$product_image_map = $this->product_map_directory->get_product_image_map_by_nettix_id_and_filename(
					$image['nettix_id'],
					$image['image_filename']
				);
			}

			// If product image map was found then update it this should also be only a migration thing
			if ( $product_image_map ) {
				$image_id = $this->fetch_wp_image_id_for_filename( $product, $image['image_filename'] );

				$this->product_map_directory->update_product_image_map(
					$product_image_map['id'],
					$product_id,
					$image['nettix_id'],
					$image['nettix_image_id'],
					$image_id,
					$image['image_filename'],
					$image['image_url'],
					true,
					$job_handler_id
				);
				continue;
			}

			$this->product_map_directory->create_product_image_map(
				$product_id,
				$image['nettix_id'],
				$image['nettix_image_id'],
				$image['image_id'],
				$image['image_filename'],
				$image['image_url'],
				(bool) $image['fetched'],
				$job_handler_id
			);
		}

		if ( ! empty( $nettix_image_ids ) ) {
			$images_to_remove = $this->product_map_directory->get_product_image_maps_not_in_array_by_nettix_image_ids( $image['nettix_id'],
				$nettix_image_ids );
			if ( ! empty( $images_to_remove ) ) {
				$this->product_map_directory->mark_images_for_removal( $images_to_remove );
			}
		}
	}

	private function fetch_wp_image_id_for_filename( WC_Product $product, string $image_filename ): ?int {
		foreach ( $product->get_gallery_image_ids() as $image_id ) {
			$image_meta_data = wp_get_attachment_image_src( $image_id );
			if ( basename( $image_meta_data[0] ) == $image_filename ) {
				return $image_id;
			}
		}

		return null;
	}

	private function parse_images_from_ad_response( array $map ): array {
		$ad_response_data = json_decode( $map['ad_data'] ?? '[]', true );
		$ret              = [];

		$nettix_id = (int) sanitize_text_field( $ad_response_data['id'] );

		foreach ( $ad_response_data['images'] ?? [] as $image ) {
			$nettix_image_id = (int) sanitize_text_field( $image['id'] );

			if ( $nettix_image_id <= 0 ) {
				continue;
			}

			if ( $existing_image_map = $this->product_map_directory->get_product_image_map_by_nettix_image_id( $nettix_image_id ) ) {
				$fetched = $existing_image_map['fetched'] ?? false;
			} else {
				$fetched = false;
			}

			$ad_image = [
				'nettix_id'       => $nettix_id,
				'nettix_image_id' => $nettix_image_id,
				'image_id'        => null,
				'fetched'         => $fetched,
			];

			if ( $image['large'] ?? null ) {
				$ad_image['image_url'] = sanitize_url( $image['large']['url'] );
			} elseif ( $image['medium'] ?? null ) {
				$ad_image['image_url'] = sanitize_url( $image['medium']['url'] );
			} else {
				continue;
			}

			$ad_image['image_filename'] = basename( $ad_image['image_url'] );

			$ret[ $nettix_image_id ] = $ad_image;
		}

		return $ret;
	}
}