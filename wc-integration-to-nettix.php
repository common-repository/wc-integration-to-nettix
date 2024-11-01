<?php
/**
 * Plugin Name:       Integration to NettiX for WooCommerce
 * Description:       Integration between WooCommerce and NettiX (https://almaajo.fi). Following Alma Ajo (previously NettiX) services can be used with this integration: Nettiauto, Nettimoto, Nettivene, Nettikaravaani, and Nettikone.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      8.0
 * Author:            Orwokki <info@orwokki.com>
 * Author URI:        https://orwokki.com
 * Developer:         Orwokki <info@orwokki.com>
 * Developer URI:     https://orwokki.com
 * Text Domain:       wc-integration-to-nettix
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 *
 * WC requires at least: 5.0
 * WC tested up to: 9.4
 *
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * Nettiauto, Nettimoto, Nettivene, Nettikaravaani, and Nettikone are registered trademarks of Alma Media Suomi Oy.
 * The mentioned registered trademarks are excluded from this license. Author of this plugin
 * doesn't have ANY relation with Alma Media Suomi Oy. By using this plugin user does NOT
 * get ANY rights to the mentioned registered trademarks.
 */

// If this file is called directly, abort.
if ( ( ! defined( 'WPINC' ) ) || ( ! defined( 'ABSPATH' ) ) ) {
	exit;
}

if ( ! function_exists( 'dbDelta' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
}

if ( ! function_exists( 'wp_get_active_network_plugins' ) ) {
	require_once( ABSPATH . 'wp-includes/ms-load.php' );
}

// Import classes
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Plugin_Settings.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Database_Settings.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Activation.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Authentication.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Base_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Nettiauto_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Nettimoto_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Nettivene_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Nettikone_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/NettiX_Client/Nettikaravaani_Client.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Storage/Product_Map_Directory.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Storage/WC_Product_Directory.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Job_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Nettiauto_Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Nettimoto_Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Nettivene_Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Nettikone_Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Nettikaravaani_Ad_Handler.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Plugin/Fetcher.php';
require_once plugin_dir_path( __FILE__ ) . 'src/classes/Admin/Api.php';

use WC_Integration_NettiX\Plugin\Activation;
use WC_Integration_NettiX\Plugin\Fetcher;
use WC_Integration_NettiX\Plugin\Job_Handler;
use WC_Integration_NettiX\Plugin\Plugin_Settings;
use WC_Integration_NettiX\Admin\Api as AdminApi;

// Test to see if WooCommerce is active (including network activated).
$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

if (
	in_array( $plugin_path, wp_get_active_and_valid_plugins() )
	|| in_array( $plugin_path, wp_get_active_network_plugins() )
) {
	function wc_integration_to_nettix_set_admin_scripts() {
		if (
			is_user_logged_in() &&
			(
				current_user_can( 'administrator' )
				|| current_user_can( 'shop_manager' )
			)
		) {
			wp_register_script( 'wc-integration-to-nettix-admin-general-functions-script',
				plugins_url( '/js/general-functions.js', __FILE__ ) );
			wp_enqueue_script( 'wc-integration-to-nettix-admin-general-functions-script' );
		}
	}

	add_action( "admin_enqueue_scripts", "wc_integration_to_nettix_set_admin_scripts" );

	$adminApi = new AdminApi;
	add_action( 'wp_ajax_wc_integration_to_nettix_test_connection', [ $adminApi, 'test_connection' ] );

	function woocommerce_integration_nettix_add_integration( $integrations ): array {
		$integrations[] = 'WC_Integration_NettiX_Integration';

		return $integrations;
	}

	function woocommerce_integration_nettix_init(): void {
		if ( class_exists( 'WC_Integration' ) ) {
			// Include our integration class.
			include_once plugin_dir_path( __FILE__ ) . 'src/classes/WC_Integration_NettiX_Integration.php';

			// Register the integration.
			add_filter( 'woocommerce_integrations', 'woocommerce_integration_nettix_add_integration' );
		}
	}

	add_action( 'plugins_loaded', 'woocommerce_integration_nettix_init' );

	$settings = new Plugin_Settings();
	$fetcher  = new Fetcher( $settings );
	$job_handler = new Job_Handler();

	add_action ( 'wp_ajax_wc_integration_nettix_run_product_handling_task',
		[$job_handler, 'run_product_handling_task' ] );
	add_action ( 'wp_ajax_nopriv_wc_integration_nettix_run_product_handling_task',
		[$job_handler, 'run_product_handling_task' ] );
	add_action ( 'wp_ajax_wc_integration_nettix_run_product_image_handling_task',
		[$job_handler, 'run_product_image_handling_task' ] );
	add_action ( 'wp_ajax_nopriv_wc_integration_nettix_run_product_image_handling_task',
		[$job_handler, 'run_product_image_handling_task' ] );

	function woocommerce_integration_nettix_cron_events() {
		// To migrate from old DB schema to new.
		$activation = new Activation();
		$activation->create_db_tables();

		$settings = new Plugin_Settings();
		$fetcher  = new Fetcher( $settings );
		$fetcher->fetch();
	}

	add_action( 'woocommerce_integration_nettix_cron_hook', 'woocommerce_integration_nettix_cron_events' );

	function woocommerce_integration_nettix_deactivate() {
		$timestamp = wp_next_scheduled( 'woocommerce_integration_nettix_cron_hook' );
		wp_unschedule_event( $timestamp, 'woocommerce_integration_nettix_cron_hook' );
	}

	register_deactivation_hook( __FILE__, 'woocommerce_integration_nettix_deactivate' );
}

function woocommerce_integration_nettix_activate() {
	$activation = new Activation();
	$activation->do_activation();
	if ( ! wp_next_scheduled( 'woocommerce_integration_nettix_cron_hook' ) ) {
		wp_schedule_event( time(), 'hourly', 'woocommerce_integration_nettix_cron_hook' );
	}
}

register_activation_hook( __FILE__, 'woocommerce_integration_nettix_activate' );

function wc_integration_to_nettix_show_wc_not_activated_notice() {
	$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

	if (
		! in_array( $plugin_path, wp_get_active_and_valid_plugins() )
		&& ! in_array( $plugin_path, wp_get_active_network_plugins() )
	) {
		echo '<div class="notice notice-error is-dismissible"><p><strong>Integration to NettiX for WooCommerce</strong> needs WooCommerce to be activated. After WooCommerce activation reactivate Integration to NettiX for WooCommerce.</p></div>';
	}
}

add_action( 'admin_notices', 'wc_integration_to_nettix_show_wc_not_activated_notice' );

function wc_integration_to_nettix_product_custom_fields() {
	global $woocommerce, $post;
	echo '<div class="product_custom_field">';
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_vehicle_type',
		'placeholder' => __( 'Vehicle type', 'wc-integration-to-nettix' ),
		'label'       => __( 'Vehicle type', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_boat_type',
		'placeholder' => __( 'Boat type', 'wc-integration-to-nettix' ),
		'label'       => __( 'Boat type', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_boat_sub_type',
		'placeholder' => __( 'Boat sub-type', 'wc-integration-to-nettix' ),
		'label'       => __( 'Boat sub-type', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_maker',
		'placeholder' => __( 'Maker', 'wc-integration-to-nettix' ),
		'label'       => __( 'Maker', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_model',
		'placeholder' => __( 'Model', 'wc-integration-to-nettix' ),
		'label'       => __( 'Model', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_model_type',
		'placeholder' => __( 'Model type', 'wc-integration-to-nettix' ),
		'label'       => __( 'Model type', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_base',
		'placeholder' => __( 'Base', 'wc-integration-to-nettix' ),
		'label'       => __( 'Base', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_year',
		'placeholder' => __( 'Year', 'wc-integration-to-nettix' ),
		'label'       => __( 'Year', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_kms',
		'placeholder' => __( 'Kilometers', 'wc-integration-to-nettix' ),
		'label'       => __( 'Kilometers', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_hours',
		'placeholder' => __( 'Hours', 'wc-integration-to-nettix' ),
		'label'       => __( 'Hours', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_fuel_type',
		'placeholder' => __( 'Fuel type', 'wc-integration-to-nettix' ),
		'label'       => __( 'Fuel type', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_engine_maker',
		'placeholder' => __( 'Engine maker', 'wc-integration-to-nettix' ),
		'label'       => __( 'Engine maker', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_engine_model',
		'placeholder' => __( 'Engine model', 'wc-integration-to-nettix' ),
		'label'       => __( 'Engine model', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_engine_model_specification',
		'placeholder' => __( 'Engine model specification', 'wc-integration-to-nettix' ),
		'label'       => __( 'Engine model specification', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	woocommerce_wp_text_input( [
		'id'          => '_wc_integration_to_nettix_location',
		'placeholder' => __( 'Location', 'wc-integration-to-nettix' ),
		'label'       => __( 'Location', 'wc-integration-to-nettix' ),
		'desc_tip'    => true
	] );
	echo '</div>';
}

add_action( 'woocommerce_product_options_general_product_data', 'wc_integration_to_nettix_product_custom_fields' );

function wc_integration_to_nettix_product_custom_fields_save( $post_id ) {
	$vehicle_type = $_POST['_wc_integration_to_nettix_vehicle_type'];
	if ( ! empty( $vehicle_type ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_vehicle_type', esc_attr( $vehicle_type ) );
	}

	$boat_type = $_POST['_wc_integration_to_nettix_boat_type'];
	if ( ! empty( $boat_type ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_boat_type', esc_attr( $boat_type ) );
	}

	$boat_sub_type = $_POST['_wc_integration_to_nettix_boat_sub_type'];
	if ( ! empty( $boat_sub_type ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_boat_sub_type', esc_attr( $boat_sub_type ) );
	}

	$maker = $_POST['_wc_integration_to_nettix_maker'];
	if ( ! empty( $maker ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_maker', esc_attr( $maker ) );
	}

	$model = $_POST['_wc_integration_to_nettix_model'];
	if ( ! empty( $model ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_model', esc_attr( $model ) );
	}

	$model_type = $_POST['_wc_integration_to_nettix_model_type'];
	if ( ! empty( $model_type ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_model_type', esc_attr( $model_type ) );
	}

	$base = $_POST['_wc_integration_to_nettix_base'];
	if ( ! empty( $base ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_base', esc_attr( $base ) );
	}

	$year = $_POST['_wc_integration_to_nettix_year'];
	if ( ! empty( $year ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_year', esc_attr( $year ) );
	}

	$kms = $_POST['_wc_integration_to_nettix_kms'];
	if ( ! empty( $kms ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_kms', esc_attr( $kms ) );
	}

	$hours = $_POST['_wc_integration_to_nettix_hours'];
	if ( ! empty( $hours ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_hours', esc_attr( $kms ) );
	}

	$fuel_type = $_POST['_wc_integration_to_nettix_fuel_type'];
	if ( ! empty( $fuel_type ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_fuel_type', esc_attr( $fuel_type ) );
	}

	$engine_maker = $_POST['_wc_integration_to_nettix_engine_maker'];
	if ( ! empty( $engine_maker ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_engine_maker', esc_attr( $engine_maker ) );
	}

	$engine_model = $_POST['_wc_integration_to_nettix_engine_model'];
	if ( ! empty( $engine_model ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_engine_model', esc_attr( $engine_model ) );
	}

	$engine_model_specification = $_POST['_wc_integration_to_nettix_engine_model_specification'];
	if ( ! empty( $engine_model_specification ) ) {
		update_post_meta( $post_id,
			'_wc_integration_to_nettix_engine_model_specification',
			esc_attr( $engine_model_specification ) );
	}

	$location = $_POST['_wc_integration_to_nettix_location'];
	if ( ! empty( $location ) ) {
		update_post_meta( $post_id, '_wc_integration_to_nettix_location', esc_attr( $location ) );
	}
}

add_action( 'woocommerce_process_product_meta', 'wc_integration_to_nettix_product_custom_fields_save' );

function wc_integration_to_nettix_before_shop_loop_item_action() {
	global $product;
	$plugin_settings = new Plugin_Settings();

	if ( $plugin_settings->get_show_badge() == 'yes' && $plugin_settings->get_badge_field() ) {
		$html = "<span id=\"wc_integration_to_nettix_badge_" . $product->get_slug() . "\" class=\"wc-integration-to-nettix-badge\">";
		$html .= $product->get_meta( $plugin_settings->get_badge_field() );
		$html .= "</span>";

		echo $html;
	}
}

add_action( 'woocommerce_before_shop_loop_item', 'wc_integration_to_nettix_before_shop_loop_item_action' );

wp_register_style('wc-integration-to-nettix-ui-styles', plugins_url('/css/wc-integration-to-nettix-ui.css', __FILE__));
wp_enqueue_style('wc-integration-to-nettix-ui-styles');

