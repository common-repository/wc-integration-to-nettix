<?php

namespace WC_Integration_NettiX\Plugin;

class Ad_Data {
	private string $slug_prefix = '';
	private int $nettix_id;
	private string $product_url;
	private string $title;
	private string $description;
	private string $short_description;
	private float $price;
	/** @var string[] URLs pointing to images of the ad */
	private array $image_urls = [];
	/** @var int[] IDs to WP Terms of product categories for the ad */
	private array $category_ids = [];
	/** @var int[] IDs to WP Terms of product tags for the ad */
	private array $tag_ids = [];

	public function get_slug_prefix(): string {
		return $this->slug_prefix;
	}

	public function set_slug_prefix( string $slug_prefix ): void {
		$this->slug_prefix = $slug_prefix;
	}

	public function get_nettix_id(): int {
		return $this->nettix_id;
	}

	public function set_nettix_id( int $nettix_id ): void {
		$this->nettix_id = $nettix_id;
	}

	public function get_product_url(): string {
		return $this->product_url;
	}

	public function set_product_url( string $product_url ): void {
		$this->product_url = $product_url;
	}

	public function get_title(): string {
		return $this->title;
	}

	public function set_title( string $title ): void {
		$this->title = $title;
	}

	public function get_description(): string {
		return $this->description;
	}

	public function set_description( string $description ): void {
		$this->description = $description;
	}

	public function get_short_description(): string {
		return $this->short_description;
	}

	public function set_short_description( string $short_description ): void {
		$this->short_description = $short_description;
	}

	public function get_price(): float {
		return $this->price;
	}

	public function set_price( float $price ): void {
		$this->price = $price;
	}

	public function get_image_urls(): array {
		return $this->image_urls;
	}

	public function set_image_urls( array $image_urls ): void {
		$this->image_urls = $image_urls;
	}

	public function add_image_url( string $image_url ): void {
		$this->image_urls[] = $image_url;
	}

	public function get_category_ids(): array {
		return $this->category_ids;
	}

	public function set_category_ids( array $category_ids ): void {
		$this->category_ids = $category_ids;
	}

	public function add_category_id( int $category_id ): void {
		$this->category_ids[] = $category_id;
	}

	public function get_tag_ids(): array {
		return $this->tag_ids;
	}

	public function set_tag_ids( array $tag_ids ): void {
		$this->tag_ids = $tag_ids;
	}

	public function add_tag_id( int $tag_id ): void {
		$this->tag_ids[] = $tag_id;
	}
}