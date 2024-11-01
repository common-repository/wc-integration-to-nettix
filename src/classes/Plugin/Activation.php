<?php

namespace WC_Integration_NettiX\Plugin;

class Activation {

	public const REQUIRED_DB_SCHEMA_VERSION = 2;
	public const OPTION_DB_SCHEMA_VERSION = 'wc-integration-nettix-db-schema-version';

	private Database_Settings $db_settings;

	public function __construct() {
		$this->db_settings = new Database_Settings();
	}

	public function do_activation() {
		$this->create_db_tables();
		$this->create_product_cats();
	}

	public function create_db_tables() {
		if ($this->get_current_db_schema_version() < self::REQUIRED_DB_SCHEMA_VERSION) {
			$this->create_table_nettix_product_map();
			$this->create_table_nettix_product_image_map();

			$this->set_current_db_schema_version(self::REQUIRED_DB_SCHEMA_VERSION);
		}
	}

	private function create_table_nettix_product_map() {
		global $wpdb;

		$sql = "CREATE TABLE " . $this->db_settings->table_product_map . " (\n";
		$sql .= "  id int NOT NULL AUTO_INCREMENT,\n";
		$sql .= "  post_id int,\n";
		$sql .= "  nettix_id int,\n";
		$sql .= "  ad_data_hash varchar(128),\n";
		$sql .= "  ad_data MEDIUMBLOB,\n";
		$sql .= "  product_created int(1) NOT NULL DEFAULT '0',\n";
		$sql .= "  product_updated int(1) NOT NULL DEFAULT '0',\n";
		$sql .= "  remove int(1) NOT NULL DEFAULT '0',\n";
		$sql .= "  job_handler int(2) NOT NULL DEFAULT '1',\n";
		$sql .= "  PRIMARY KEY  (id),\n";
		$sql .= "  KEY post_id (post_id),\n";
		$sql .= "  KEY nettix_id (nettix_id),\n";
		$sql .= "  KEY product_created (product_created),\n";
		$sql .= "  KEY product_updated (product_updated),\n";
		$sql .= "  KEY job_handler (job_handler)\n";
		$sql .= ") " . $wpdb->get_charset_collate() . ";";

		dbDelta( $sql );
	}

	private function create_table_nettix_product_image_map() {
		global $wpdb;

		$sql = "CREATE TABLE " . $this->db_settings->table_product_image_map . " (\n";
		$sql .= "  id int NOT NULL AUTO_INCREMENT,\n";
		$sql .= "  post_id int,\n";
		$sql .= "  nettix_id int,\n";
		$sql .= "  nettix_image_id int,\n";
		$sql .= "  image_id int,\n";
		$sql .= "  image_filename varchar(192),\n";
		$sql .= "  image_url varchar(255),\n";
		$sql .= "  fetched int(1) NOT NULL DEFAULT '0',\n";
		$sql .= "  remove int(1) NOT NULL DEFAULT '0',\n";
		$sql .= "  job_handler int(2) NOT NULL DEFAULT '1',\n";
		$sql .= "  PRIMARY KEY  (id),\n";
		$sql .= "  KEY post_id (post_id),\n";
		$sql .= "  KEY nettix_id (nettix_id),\n";
		$sql .= "  KEY image_id (image_id),\n";
		$sql .= "  KEY image_filename (image_filename),\n";
		$sql .= "  KEY fetched (fetched),\n";
		$sql .= "  KEY job_handler (job_handler)\n";
		$sql .= ") " . $wpdb->get_charset_collate() . ";";

		dbDelta( $sql );
	}

	private function create_product_cats() {
		if ( ! term_exists( 'NettiMoto', 'product_cat' ) ) {
			wp_insert_term( 'NettiMoto',
				'product_cat',
				[
					'slug'        => 'nettimoto',
					'description' => __( 'Product category for motor bike ads imported from Nettimoto by NettiX integration',
						'wc-integration-to-nettix' )
				] );
		}
		if ( ! term_exists( 'NettiVene', 'product_cat' ) ) {
			wp_insert_term( 'NettiVene',
				'product_cat',
				[
					'slug'        => 'nettivene',
					'description' => __( 'Product category for boat ads imported from Nettivene by NettiX integration',
						'wc-integration-to-nettix' )
				] );
		}
		if ( ! term_exists( 'NettiAuto', 'product_cat' ) ) {
			wp_insert_term( 'NettiAuto',
				'product_cat',
				[
					'slug'        => 'nettiauto',
					'description' => __( 'Product category for car ads imported from Nettiauto by NettiX integration',
						'wc-integration-to-nettix' )
				] );
		}
		if ( ! term_exists( 'NettiKone', 'product_cat' ) ) {
			wp_insert_term( 'NettiKone',
				'product_cat',
				[
					'slug'        => 'nettikone',
					'description' => __( 'Product category for machine ads imported from Nettikone by NettiX integration',
						'wc-integration-to-nettix' )
				] );
		}
		if ( ! term_exists( 'NettiKaravaani', 'product_cat' ) ) {
			wp_insert_term( 'NettiKaravaani',
				'product_cat',
				[
					'slug'        => 'nettikaravaani',
					'description' => __( 'Product category for machine ads imported from Nettikaravaani by NettiX integration',
						'wc-integration-to-nettix' )
				] );
		}
	}

	/**
	 * @return int
	 */
	private function get_current_db_schema_version(): int {
		return get_option( self::OPTION_DB_SCHEMA_VERSION ) ?: 1;
	}

	/**
	 * @param int $version
	 *
	 * @return void
	 */
	private function set_current_db_schema_version( int $version ): void {
		update_option( self::OPTION_DB_SCHEMA_VERSION, $version );
	}
}