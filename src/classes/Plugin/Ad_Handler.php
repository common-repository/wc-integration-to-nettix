<?php

namespace WC_Integration_NettiX\Plugin;

use Exception;
use WC_Integration_NettiX\Storage\Product_Map_Directory;
use WC_Integration_NettiX\Storage\WC_Product_Directory;
use WP_Term;

abstract class Ad_Handler {
	protected array $terms_cache = [];
	protected Plugin_Settings $settings;
	protected Database_Settings $database_settings;
	protected Fetcher $fetcher;
	protected Product_Map_Directory $product_map_directory;
	protected WC_Product_Directory $wc_product_directory;
	protected Job_Handler $job_handler;

	public function __construct(
		Plugin_Settings $settings,
		?Fetcher $fetcher = null
	) {
		$this->settings              = $settings;
		$this->fetcher               = $fetcher ?? new Fetcher( $settings );
		$this->database_settings     = new Database_Settings();
		$this->product_map_directory = new Product_Map_Directory( $this->database_settings );
		$this->wc_product_directory  = new WC_Product_Directory();
		$this->job_handler           = new Job_Handler( $this->product_map_directory, $this->wc_product_directory );
	}

	abstract protected function populate_wc_data( array &$ad_response_data ): void;

	abstract public function get_main_category(): WP_Term;

	/**
	 * @param array $response_array
	 *
	 * @return void
	 * @throws Exception
	 */
	public function handle( array $response_array ): void {
		$job_handler_id           = 1;
		$nettix_ids_not_to_remove = [];

		foreach ( $response_array as $ad_response_data ) {
			if ( sanitize_text_field( $ad_response_data['status'] ) == 'forsale' ) {
				if ( $job_handler_id > Job_handler::MAX_PRODUCT_UPDATE_TASKS ) {
					$job_handler_id = 1;
				}

				try {
					$this->populate_wc_data( $ad_response_data );
				} catch ( Exception $e ) {
					continue;
				}

				$this->create_or_update_product_map( $ad_response_data, $job_handler_id );

				$nettix_ids_not_to_remove[] = (int) $ad_response_data['id'];

				$job_handler_id ++;
			} else {
				$this->product_map_directory->mark_product_for_removal( $ad_response_data['id'] );
			}
		}

		$this->mark_products_for_removal( $nettix_ids_not_to_remove );
		$this->job_handler->start_product_handling();
	}

	protected function get_maker_category( string $maker ): WP_Term {
		$maker      = $this->sanitize_tag_name( $maker );
		$maker_slug = $this->create_name_slug( $maker );

		try {
			return $this->get_category( $maker_slug );
		} catch ( Exception $e ) {
			try {
				$main_category = $this->get_main_category();
				$this->create_category( $maker, $main_category );

				return $this->get_category( $maker_slug );
			} catch ( Exception $e ) {
				throw new Exception( "Could not create maker category for {$maker}" );
			}
		}
	}

	/**
	 * @param string $category_slug
	 *
	 * @return WP_Term
	 * @throws Exception
	 */
	protected function get_category( string $category_slug ): WP_Term {
		if ( ! array_key_exists( $category_slug, $this->terms_cache ) ) {
			$term = get_term_by( 'slug', $category_slug, 'product_cat' );

			if ( ! ( $term instanceof WP_Term ) ) {
				throw new Exception( "Could not find term for category slug '" . $category_slug . "'" );
			}

			$this->terms_cache[ $category_slug ] = $term;
		}

		return $this->terms_cache[ $category_slug ];
	}

	/**
	 * Returns tag as WP_Term. If term doesn't exist it's created. Uses local terms cache.
	 *
	 * @param string $tag_name
	 *
	 * @return WP_Term|null
	 * @throws Exception
	 */
	protected function get_tag( string $tag_name ): ?WP_Term {
		if ( ! $tag_name ) {
			return null;
		}

		$tag_name_slug = $this->create_name_slug( $tag_name );
		$tag_name      = $this->sanitize_tag_name( $tag_name );

		if ( ! array_key_exists( $tag_name_slug, $this->terms_cache ) ) {
			if ( ! term_exists( $tag_name, 'product_tag' ) ) {
				$this->create_tag( $tag_name );
			}

			$res = get_term_by( 'slug', $tag_name_slug, 'product_tag' );

			if ( $res instanceof WP_Term ) {
				$this->terms_cache[ $tag_name_slug ] = $res;
			} else {
				return null;
			}
		}

		return $this->terms_cache[ $tag_name_slug ];
	}

	/**
	 * @param string $tag_name
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function create_tag( string $tag_name ): void {
		$tag_name_slug = $this->create_name_slug( $tag_name );

		$res = wp_insert_term( $tag_name,
			'product_tag',
			[ 'slug' => $tag_name_slug, 'description' => 'Product tag for ' . $tag_name ] );

		if ( is_wp_error( $res ) ) {
			throw new Exception( "Failed to add product tag for '" . $tag_name . "'" );
		}
	}

	/**
	 * @param string $category_name
	 * @param WP_Term|null $parent_category
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function create_category( string $category_name, ?WP_Term $parent_category = null ): void {
		$category_name_slug = $this->create_name_slug( $category_name );

		$res = wp_insert_term( $category_name,
			'product_cat',
			[
				'slug'        => $category_name_slug,
				'description' => 'Product category for ' . $category_name,
				'parent'      => ( $parent_category ) ? $parent_category->term_id : 0
			] );

		if ( is_wp_error( $res ) ) {
			throw new Exception( "Failed to add product category for '" . $category_name . "'" );
		}
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	protected function create_name_slug( string $name ): string {
		return str_replace( [ "`", "´", "'", "\"", ",", ";", ":", ".", " " ],
			[ "", "", "", "", "", "", "", "-", "-" ],
			strtolower( $name ) );
	}

	protected function sanitize_tag_name( string $tag_name ): string {
		return str_replace( [ ",", ".", ";", ":" ], "", $tag_name );
	}

	/**
	 * Creates new external product to WooCommerce if one doesn't already exist. If product already exists the existing product is updated with given data.
	 *
	 * @param array $ad_response_data
	 * @param int $job_handler_id
	 *
	 * @return void
	 */
	protected function create_or_update_product_map( array $ad_response_data, int $job_handler_id ): void {
		if ( $product_map = $this->product_map_directory->get_product_map_by_nettix_id( $ad_response_data['id'] ) ) {
			$this->update_product_map( $product_map, $ad_response_data, $job_handler_id );
		} else {
			$this->create_product_map( $ad_response_data, $job_handler_id );
		}
	}

	/**
	 * @param array $nettix_ids_not_to_remove
	 *
	 * @return void
	 */
	private function mark_products_for_removal( array $nettix_ids_not_to_remove ): void {
		$maps_to_remove = $this->product_map_directory->get_product_maps_not_in_array_by_nettix_ids( $nettix_ids_not_to_remove );

		if ( count( $maps_to_remove ) == 0 ) {
			return;
		}

		$this->product_map_directory->mark_products_for_removal( $maps_to_remove );
	}

	/**
	 * @param array $product_map
	 * @param array $ad_response_data
	 * @param int $job_handler_id
	 *
	 * @return void
	 */
	private function update_product_map( array $product_map, array $ad_response_data, int $job_handler_id ): void {
		$ad_response_data_json = json_encode( $ad_response_data );
		$ad_data_hash          = hash( 'sha256', $ad_response_data_json );

		if ( $product_map['ad_data_hash'] == $ad_data_hash ) {
			// Nothing to update
			return;
		}

		$this->product_map_directory->update_product_map(
			$product_map['id'],
			$product_map['post_id'],
			$ad_response_data['id'],
			$ad_data_hash,
			$ad_response_data_json,
			true,
			false,
			$job_handler_id
		);
	}

	/**
	 * @param array $ad_response_data
	 * @param int $job_handler_id
	 *
	 * @return void
	 */
	private function create_product_map( array $ad_response_data, int $job_handler_id ): void {
		$ad_response_data_json = json_encode( $ad_response_data );
		$ad_data_hash          = hash( 'sha256', $ad_response_data_json );

		$this->product_map_directory->create_product_map(
			null,
			$ad_response_data['id'],
			$ad_data_hash,
			$ad_response_data_json,
			false,
			true,
			$job_handler_id
		);
	}

	protected function format_kilometers( int $kilometers ): string {
		return ( $kilometers == 0 ) ? "0 km" : ceil( $kilometers / 1000 ) . " tkm";
	}

	protected function format_hours( int $hours ): string {
		return sprintf( __( "%s use hours" ), $hours );
	}
}