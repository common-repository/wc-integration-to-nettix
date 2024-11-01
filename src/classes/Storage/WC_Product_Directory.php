<?php

namespace WC_Integration_NettiX\Storage;

use Exception;
use WC_Product;
use WC_Product_External;
use WC_Product_Query;

class WC_Product_Directory {
	/**
	 * Creates new WooCommerce product based on ad data fetched from NettiX.
	 * Type of created product is 'external'.
	 *
	 * @param array $map
	 *
	 * @return WC_Product
	 */
	public function create_product( array $map ): WC_Product {
		$ad_response_data = json_decode($map['ad_data'] ?? '[]', true);

		$title = $ad_response_data['wc_data']['title'] ?? '';
		$price = $ad_response_data['price'] ?? 0.0;
		$url = $ad_response_data['adUrl'] ?? '';
		$slug_prefix = $ad_response_data['wc_data']['slug_prefix'] ?? '';
		$nettix_id = $ad_response_data['id'];
		$description = $ad_response_data['description'] ?? '';
		$short_description = $ad_response_data['wc_data']['short_description'] ?? '';
		$category_ids = $ad_response_data['wc_data']['category_ids'] ?? [];
		$tag_ids = $ad_response_data['wc_data']['tag_ids'] ?? [];

		$product = new WC_Product_External();
		$product->set_name( $title );
		$product->set_regular_price( $price );
		$product->set_product_url( $url );
		$product->set_button_text( 'Avaa' );
		$product->set_status( 'publish' );
		$product->set_slug( $slug_prefix . '-' . $nettix_id );
		$product->set_description( $description );
		$product->set_short_description( $short_description );
		$product->set_category_ids( $category_ids );
		$product->set_tag_ids( $tag_ids );

		$product->save();

		foreach ( $ad_response_data['wc_data']['custom_fields'] ?? [] as $key => $value ) {
			update_post_meta( $product->get_id(), $key, $value );
		}

		return $product;
	}

	/**
	 * Updates existing WooCommerce product based on data fetched from NettiX.
	 *
	 * @param array $map
	 *
	 * @return WC_Product
	 * @throws Exception
	 */
	public function update_product(
		array $map
	): WC_Product {
		if (!$product = $this->get_product_by_id($map['post_id'])) {
			throw new Exception("Product not found");
		}

		$ad_response_data = json_decode($map['ad_data'] ?? '[]', true);

		$title = $ad_response_data['wc_data']['title'] ?? '';
		$price = $ad_response_data['price'] ?? 0.0;
		$description = $ad_response_data['description'] ?? '';
		$short_description = $ad_response_data['wc_data']['short_description'] ?? '';
		$category_ids = $ad_response_data['wc_data']['category_ids'] ?? [];
		$tag_ids = $ad_response_data['wc_data']['tag_ids'] ?? [];

		$product->set_name( $title );
		$product->set_regular_price( $price );
		$product->set_description( $description );
		$product->set_short_description( $short_description );
		$product->set_category_ids( $category_ids );
		$product->set_tag_ids( $tag_ids );

		$product->save();

		foreach ( $ad_response_data['wc_data']['custom_fields'] ?? [] as $key => $value ) {
			update_post_meta( $product->get_id(), $key, $value );
		}

		return $product;
	}

	/**
	 * @param int $product_id
	 *
	 * @return WC_Product|null
	 */
	public function get_product_by_id(int $product_id): ?WC_Product {
		$query    = new WC_Product_Query( [ 'include' => [ $product_id ] ] );
		$products = $query->get_products();

		return $products[0] ?? null;
	}
}