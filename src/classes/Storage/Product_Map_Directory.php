<?php

namespace WC_Integration_NettiX\Storage;

use WC_Integration_NettiX\Plugin\Database_Settings;

class Product_Map_Directory {
	private Database_Settings $database_settings;

	public function __construct( ?Database_Settings $database_settings = null ) {
		$this->database_settings = $database_settings ?? new Database_Settings();
	}

	public function get_product_map_by_id( int $id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_map . " WHERE id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	public function get_product_map_by_nettix_id( int $nettix_id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_map . " WHERE nettix_id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $nettix_id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	public function get_product_map_by_product_id( int $product_id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_map . " WHERE post_id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $product_id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	/**
	 * @param int[] $nettix_ids
	 *
	 * @return array
	 */
	public function get_product_maps_not_in_array_by_nettix_ids( array $nettix_ids ): array {
		global $wpdb;

		$query = "SELECT nettix_id FROM " . $this->database_settings->table_product_map;

		if ( ! empty( $nettix_ids ) ) {
			$query .= " WHERE nettix_id NOT IN (" . implode( ",", $nettix_ids ) . ")";
		}

		$result = $wpdb->get_results( $query, ARRAY_A );

		return array_column( $result, 'nettix_id' );
	}

	/**
	 * @param int $max_amount
	 *
	 * @return array
	 */
	public function get_product_maps_to_create_or_update( int $max_amount = 1 ): array {
		global $wpdb;

		$query = "SELECT * FROM " . $this->database_settings->table_product_map . " WHERE product_created = 0 OR product_updated = 0 OR remove = 1 ORDER BY id DESC LIMIT %d";

		return $wpdb->get_results( $wpdb->prepare( $query, $max_amount ), ARRAY_A );
	}

	/**
	 * @param int $job_handler
	 *
	 * @return array
	 */
	public function get_product_map_to_handle_for_job_handler( int $job_handler = 1 ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_map . " WHERE job_handler = %d AND (product_created = 0 OR product_updated = 0 OR remove = 1) ORDER BY id LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $job_handler ), ARRAY_A );

		return $result[0] ?? null;
	}

	/**
	 * @param int|null $product_id
	 * @param int $nettix_id
	 * @param string $ad_data_hash
	 * @param string $ad_data
	 * @param bool $product_created
	 * @param bool $product_updated
	 * @param int $job_handler
	 *
	 * @return void
	 */
	public function create_product_map(
		?int $product_id,
		int $nettix_id,
		string $ad_data_hash,
		string $ad_data,
		bool $product_created,
		bool $product_updated,
		int $job_handler = 1
	): void {
		global $wpdb;

		$wpdb->insert( $this->database_settings->table_product_map,
			[
				'post_id'         => $product_id,
				'nettix_id'       => $nettix_id,
				'ad_data_hash'    => $ad_data_hash,
				'ad_data'         => $ad_data,
				'product_created' => ( $product_created ) ? 1 : 0,
				'product_updated' => ( $product_updated ) ? 1 : 0,
				'job_handler'     => $job_handler,
			] );
	}

	/**
	 * @param int $nettix_id
	 * @param int $product_id
	 *
	 * @return void
	 */
	public function set_product_id_to_map( int $nettix_id, int $product_id ): void {
		global $wpdb;

		$wpdb->update( $this->database_settings->table_product_map,
			[
				'post_id'         => $product_id,
				'product_created' => 1,
			],
			[
				'nettix_id' => $nettix_id,
			] );
	}

	/**
	 * @param int $id
	 * @param int|null $product_id
	 * @param int $nettix_id
	 * @param string $ad_data_hash
	 * @param string $ad_data
	 * @param bool $product_created
	 * @param bool $product_updated
	 * @param int $job_handler
	 *
	 * @return void
	 */
	public function update_product_map(
		int $id,
		?int $product_id,
		int $nettix_id,
		string $ad_data_hash,
		string $ad_data,
		bool $product_created,
		bool $product_updated,
		int $job_handler = 1
	): void {
		global $wpdb;

		$wpdb->update( $this->database_settings->table_product_map,
			[
				'post_id'         => $product_id,
				'nettix_id'       => $nettix_id,
				'ad_data_hash'    => $ad_data_hash,
				'ad_data'         => $ad_data,
				'product_created' => ( $product_created ) ? 1 : 0,
				'product_updated' => ( $product_updated ) ? 1 : 0,
				'job_handler'     => $job_handler,
			],
			[
				'id' => $id,
			] );
	}

	public function mark_product_for_removal( int $nettix_id ): void {
		global $wpdb;

		$wpdb->update( $this->database_settings->table_product_map, [ 'remove' => 1 ], [ 'nettix_id' => $nettix_id ] );
	}

	public function mark_products_for_removal( array $nettix_ids ): void {
		global $wpdb;

		$query = "UPDATE " . $this->database_settings->table_product_map . " SET remove = 1 WHERE nettix_id IN (" . implode( ",",
				$nettix_ids ) . ")";
		$wpdb->query( $query );
	}

	/**
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function remove_product_maps( int $post_id ) {
		global $wpdb;

		$wpdb->delete( $this->database_settings->table_product_map, [ 'post_id' => $post_id ] );
	}

	/**
	 * @param int $id
	 *
	 * @return array|null
	 */
	public function get_product_image_map_by_id( int $id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	/**
	 * @param int $image_id
	 *
	 * @return array|null
	 */
	public function get_product_image_map_by_image_id( int $image_id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE image_id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $image_id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	/**
	 * @param int $image_id
	 *
	 * @return array|null
	 */
	public function get_product_image_map_by_nettix_image_id( int $nettix_image_id ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE nettix_image_id = %d ORDER BY id DESC LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $nettix_image_id ), ARRAY_A );

		if ( count( $result ) < 1 ) {
			return null;
		}

		return $result[0] ?? null;
	}

	/**
	 * @param int $nettix_id
	 * @param string $image_filename
	 *
	 * @return array
	 */
	public function get_product_image_map_by_nettix_id_and_filename( int $nettix_id, string $image_filename ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE nettix_id = %d AND image_filename = '%s' ORDER BY id LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $nettix_id, $image_filename ), ARRAY_A ) ?? [];

		return $result[0] ?? null;
	}

	public function get_product_image_map_by_product_id( int $product_id ): array {
		global $wpdb;

		$query = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE post_id = %d ORDER BY id";

		return $wpdb->get_results( $wpdb->prepare( $query, $product_id ), ARRAY_A ) ?? [];
	}

	/**
	 * @param int $product_id
	 * @param int $nettix_id
	 * @param int $nettix_image_id
	 * @param int|null $image_id
	 * @param string $image_filename
	 * @param string $image_url
	 * @param bool $fetched
	 * @param int $job_handler
	 *
	 * @return void
	 */
	public function create_product_image_map(
		int $product_id,
		int $nettix_id,
		int $nettix_image_id,
		?int $image_id,
		string $image_filename,
		string $image_url,
		bool $fetched,
		int $job_handler = 1
	): void {
		global $wpdb;

		$wpdb->insert( $this->database_settings->table_product_image_map,
			[
				'post_id'         => $product_id,
				'nettix_id'       => $nettix_id,
				'nettix_image_id' => $nettix_image_id,
				'image_id'        => $image_id,
				'image_filename'  => $image_filename,
				'image_url'       => $image_url,
				'fetched'         => ( $fetched ) ? 1 : 0,
				'job_handler'     => $job_handler,
			] );
	}

	/**
	 * @param int $image_id
	 *
	 * @return void
	 */
	public function set_product_image_fetched(
		int $image_map_id,
		int $image_id
	): void {
		global $wpdb;

		$wpdb->update( $this->database_settings->table_product_image_map,
			[
				'image_id' => $image_id,
				'fetched'  => 1,
			],
			[
				'id' => $image_map_id,
			] );
	}

	/**
	 * @param int $id
	 * @param int $product_id
	 * @param int $nettix_id
	 * @param int $nettix_image_id
	 * @param int|null $image_id
	 * @param string $image_filename
	 * @param string $image_url
	 * @param bool $fetched
	 * @param int $job_handler
	 *
	 * @return void
	 */
	public function update_product_image_map(
		int $id,
		int $product_id,
		int $nettix_id,
		int $nettix_image_id,
		?int $image_id,
		string $image_filename,
		string $image_url,
		bool $fetched,
		int $job_handler = 1
	): void {
		global $wpdb;

		$wpdb->update( $this->database_settings->table_product_image_map,
			[
				'post_id'         => $product_id,
				'nettix_id'       => $nettix_id,
				'nettix_image_id' => $nettix_image_id,
				'image_id'        => $image_id,
				'image_filename'  => $image_filename,
				'image_url'       => $image_url,
				'fetched'         => ( $fetched ) ? 1 : 0,
				'job_handler'     => $job_handler,
			],
			[
				'id' => $id,
			] );
	}

	/**
	 * @param int $max_amount
	 *
	 * @return array
	 */
	public function get_product_image_maps_to_handle( int $max_amount = 1 ): array {
		global $wpdb;

		$query = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE fetched = 0 OR remove = 1 ORDER BY id DESC LIMIT %d";

		return $wpdb->get_results( $wpdb->prepare( $query, $max_amount ), ARRAY_A );
	}

	public function get_product_image_map_to_handle_for_job_handler( int $job_handler = 1 ): ?array {
		global $wpdb;

		$query  = "SELECT * FROM " . $this->database_settings->table_product_image_map . " WHERE (fetched = 0 OR remove = 1) AND job_handler = %d ORDER BY id LIMIT 1";
		$result = $wpdb->get_results( $wpdb->prepare( $query, $job_handler ), ARRAY_A );

		return $result[0] ?? null;
	}

	/**
	 * @param int $post_id
	 * @param int $image_id
	 *
	 * @return void
	 */
	public function remove_product_image_map( int $post_id, int $image_id ) {
		global $wpdb;

		$wpdb->delete( $this->database_settings->table_product_image_map,
			[ 'post_id' => $post_id, 'image_id' => $image_id ] );
	}

	/**
	 * @param int $post_id
	 *
	 * @return void
	 */
	public function remove_product_image_maps( int $post_id ) {
		global $wpdb;

		$wpdb->delete( $this->database_settings->table_product_image_map, [ 'post_id' => $post_id ] );
	}

	/**
	 * @param int $nettix_id
	 * @param array $nettix_image_ids
	 *
	 * @return array
	 */
	public function get_product_image_maps_not_in_array_by_nettix_image_ids(
		int $nettix_id,
		array $nettix_image_ids
	): array {
		global $wpdb;

		$query = "SELECT nettix_image_id FROM " . $this->database_settings->table_product_image_map . " WHERE nettix_id = %d";

		if ( ! empty( $nettix_image_ids ) ) {
			$query .= " AND nettix_image_id NOT IN (" . implode( ",", $nettix_image_ids ) . ")";
		}

		$result = $wpdb->get_results( $wpdb->prepare( $query, $nettix_id ), ARRAY_A );

		return array_column( $result, 'nettix_image_id' );
	}

	public function mark_images_for_removal( array $nettix_image_ids ): void {
		global $wpdb;

		$query = "UPDATE " . $this->database_settings->table_product_image_map . " SET remove = 1 WHERE nettix_image_id IN (" . implode( ",",
				$nettix_image_ids ) . ")";
		$wpdb->query( $query );
	}
}