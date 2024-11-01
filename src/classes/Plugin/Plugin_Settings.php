<?php

namespace WC_Integration_NettiX\Plugin;

class Plugin_Settings {
	private ?array $settings = [];

	public function __construct( ?array $settings = null ) {
		$this->settings = $settings ?? get_option( 'woocommerce_wc-integration-to-nettix_settings' ) ?: [];
	}

	// General settings

	public function get_client_id(): string {
		return $this->settings['nettix_client_id'] ?? '';
	}

	public function get_client_secret(): string {
		return $this->settings['nettix_secret_id'] ?? '';
	}

	public function get_user_ids(): string {
		return $this->settings['nettix_user_ids'] ?? '';
	}

	public function get_service_bike(): string {
		return $this->settings['nettix_service_bike'] ?? '';
	}

	public function get_service_boat(): string {
		return $this->settings['nettix_service_boat'] ?? '';
	}

	public function get_service_car(): string {
		return $this->settings['nettix_service_car'] ?? '';
	}

	public function get_service_machine(): string {
		return $this->settings['nettix_service_machine'] ?? '';
	}

	public function get_service_caravan(): string {
		return $this->settings['nettix_service_caravan'] ?? '';
	}

	public function get_show_badge(): string {
		return $this->settings['nettix_show_badge'] ?? '';
	}

	public function get_badge_field(): string {
		return $this->settings['nettix_badge_field'] ?? '';
	}

	// Nettiauto settings

	public function get_service_car_short_desc_use_list(): string {
		return $this->settings['nettix_service_car_short_desc_use_list'] ?? '';
	}

	public function get_service_car_short_desc_use_labels(): string {
		return $this->settings['nettix_service_car_short_desc_use_labels'] ?? '';
	}

	public function get_service_car_short_desc_field_maker(): string {
		return $this->settings['nettix_service_car_short_desc_field_maker'] ?? '';
	}

	public function get_service_car_short_desc_field_model(): string {
		return $this->settings['nettix_service_car_short_desc_field_model'] ?? '';
	}

	public function get_service_car_short_desc_field_model_type(): string {
		return $this->settings['nettix_service_car_short_desc_field_model_type'] ?? '';
	}

	public function get_service_car_short_desc_field_year(): string {
		return $this->settings['nettix_service_car_short_desc_field_year'] ?? '';
	}

	public function get_service_car_short_desc_field_kms(): string {
		return $this->settings['nettix_service_car_short_desc_field_kms'] ?? '';
	}

	public function get_service_car_short_desc_field_fuel_type(): string {
		return $this->settings['nettix_service_car_short_desc_field_fuel_type'] ?? '';
	}

	public function get_service_car_short_desc_field_location(): string {
		return $this->settings['nettix_service_car_short_desc_field_location'] ?? '';
	}

	// Nettikaravaani settings

	public function get_service_caravan_short_desc_use_list(): string {
		return $this->settings['nettix_service_caravan_short_desc_use_list'] ?? '';
	}

	public function get_service_caravan_short_desc_use_labels(): string {
		return $this->settings['nettix_service_caravan_short_desc_use_labels'] ?? '';
	}

	public function get_service_caravan_short_desc_field_vehicle_type(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_vehicle_type'] ?? '';
	}

	public function get_service_caravan_short_desc_field_maker(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_maker'] ?? '';
	}

	public function get_service_caravan_short_desc_field_model(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_model'] ?? '';
	}

	public function get_service_caravan_short_desc_field_model_info(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_model_info'] ?? '';
	}

	public function get_service_caravan_short_desc_field_base(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_base'] ?? '';
	}

	public function get_service_caravan_short_desc_field_year(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_year'] ?? '';
	}

	public function get_service_caravan_short_desc_field_kms(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_kms'] ?? '';
	}

	public function get_service_caravan_short_desc_field_location(): string {
		return $this->settings['nettix_service_caravan_short_desc_field_location'] ?? '';
	}

	// Nettikone settings

	public function get_service_machine_short_desc_use_list(): string {
		return $this->settings['nettix_service_machine_short_desc_use_list'] ?? '';
	}

	public function get_service_machine_short_desc_use_labels(): string {
		return $this->settings['nettix_service_machine_short_desc_use_labels'] ?? '';
	}

	public function get_service_machine_short_desc_field_maker(): string {
		return $this->settings['nettix_service_machine_short_desc_field_maker'] ?? '';
	}

	public function get_service_machine_short_desc_field_model(): string {
		return $this->settings['nettix_service_machine_short_desc_field_model'] ?? '';
	}

	public function get_service_machine_short_desc_field_model_type(): string {
		return $this->settings['nettix_service_machine_short_desc_field_model_type'] ?? '';
	}

	public function get_service_machine_short_desc_field_fuel_type(): string {
		return $this->settings['nettix_service_machine_short_desc_field_fuel_type'] ?? '';
	}

	public function get_service_machine_short_desc_field_year(): string {
		return $this->settings['nettix_service_machine_short_desc_field_year'] ?? '';
	}

	public function get_service_machine_short_desc_field_kms(): string {
		return $this->settings['nettix_service_machine_short_desc_field_kms'] ?? '';
	}

	public function get_service_machine_short_desc_field_hours(): string {
		return $this->settings['nettix_service_machine_short_desc_field_hours'] ?? '';
	}

	public function get_service_machine_short_desc_field_location(): string {
		return $this->settings['nettix_service_machine_short_desc_field_location'] ?? '';
	}

	// Nettimoto settings

	public function get_service_bike_short_desc_use_list(): string {
		return $this->settings['nettix_service_bike_short_desc_use_list'] ?? '';
	}

	public function get_service_bike_short_desc_use_labels(): string {
		return $this->settings['nettix_service_bike_short_desc_use_labels'] ?? '';
	}

	public function get_service_bike_short_desc_field_maker(): string {
		return $this->settings['nettix_service_bike_short_desc_field_maker'] ?? '';
	}

	public function get_service_bike_short_desc_field_model(): string {
		return $this->settings['nettix_service_bike_short_desc_field_model'] ?? '';
	}

	public function get_service_bike_short_desc_field_model_type(): string {
		return $this->settings['nettix_service_bike_short_desc_field_model_type'] ?? '';
	}

	public function get_service_bike_short_desc_field_year(): string {
		return $this->settings['nettix_service_bike_short_desc_field_year'] ?? '';
	}

	public function get_service_bike_short_desc_field_kms(): string {
		return $this->settings['nettix_service_bike_short_desc_field_kms'] ?? '';
	}

	public function get_service_bike_short_desc_field_location(): string {
		return $this->settings['nettix_service_bike_short_desc_field_location'] ?? '';
	}

	// Nettivene settings

	public function get_service_boat_short_desc_use_list(): string {
		return $this->settings['nettix_service_boat_short_desc_use_list'] ?? '';
	}

	public function get_service_boat_short_desc_use_labels(): string {
		return $this->settings['nettix_service_boat_short_desc_use_labels'] ?? '';
	}

	public function get_service_boat_short_desc_field_boat_type(): string {
		return $this->settings['nettix_service_boat_short_desc_field_boat_type'] ?? '';
	}

	public function get_service_boat_short_desc_field_boat_sub_type(): string {
		return $this->settings['nettix_service_boat_short_desc_field_boat_sub_type'] ?? '';
	}

	public function get_service_boat_short_desc_field_maker(): string {
		return $this->settings['nettix_service_boat_short_desc_field_maker'] ?? '';
	}

	public function get_service_boat_short_desc_field_model(): string {
		return $this->settings['nettix_service_boat_short_desc_field_model'] ?? '';
	}

	public function get_service_boat_short_desc_field_engine_maker(): string {
		return $this->settings['nettix_service_boat_short_desc_field_engine_maker'] ?? '';
	}

	public function get_service_boat_short_desc_field_engine_model(): string {
		return $this->settings['nettix_service_boat_short_desc_field_engine_model'] ?? '';
	}

	public function get_service_boat_short_desc_field_engine_model_specification(): string {
		return $this->settings['nettix_service_boat_short_desc_field_engine_model_specification'] ?? '';
	}

	public function get_service_boat_short_desc_field_year(): string {
		return $this->settings['nettix_service_boat_short_desc_field_year'] ?? '';
	}

	public function get_service_boat_short_desc_field_location(): string {
		return $this->settings['nettix_service_boat_short_desc_field_location'] ?? '';
	}
}