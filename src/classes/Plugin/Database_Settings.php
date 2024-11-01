<?php

namespace WC_Integration_NettiX\Plugin;

class Database_Settings {
	private const PLUGIN_TABLE_PREFIX = 'wc_nettix_';
	private const TABLE_PRODUCT_MAP = 'product_map';
	private const TABLE_PRODUCT_IMAGE_MAP = 'product_image_map';

	public string $table_product_map;
	public string $table_product_image_map;

	public function __construct() {
		global $wpdb;

		$this->table_product_map       = $wpdb->prefix . self::PLUGIN_TABLE_PREFIX . self::TABLE_PRODUCT_MAP;
		$this->table_product_image_map = $wpdb->prefix . self::PLUGIN_TABLE_PREFIX . self::TABLE_PRODUCT_IMAGE_MAP;
	}
}