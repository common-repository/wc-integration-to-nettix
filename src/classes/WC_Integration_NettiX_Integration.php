<?php

if ( ! class_exists( 'Wc_Integration_NettiX_Integration' ) ) {
	class WC_Integration_NettiX_Integration extends WC_Integration {
		/**
		 * Init and hook in the integration.
		 */
		public function __construct() {
			global $woocommerce;
			$this->id                 = 'wc-integration-to-nettix';
			$this->method_title       = __( 'Integration to NettiX', 'wc-integration-to-nettix' );
			$this->method_description = __( 'Integration between WooCommerce and NettiX.',
				'wc-integration-to-nettix' );
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.

			// General settings
			$this->client_id       = $this->get_option( 'nettix_client_id' );
			$this->client_secret   = $this->get_option( 'nettix_secret_id' );
			$this->user_ids        = $this->get_option( 'nettix_user_ids' );
			$this->service_boat    = $this->get_option( 'nettix_service_boat' );
			$this->service_car     = $this->get_option( 'nettix_service_car' );
			$this->service_bike    = $this->get_option( 'nettix_service_bike' );
			$this->service_machine = $this->get_option( 'nettix_service_machine' );
			$this->service_caravan = $this->get_option( 'nettix_service_caravan' );
            $this->show_badge      = $this->get_option( 'nettix_show_badge' );
            $this->badge_field     = $this->get_option( 'nettix_badge_field' );

			// Nettiauto - settings
			$this->service_car_short_desc_use_list         = $this->get_option( 'nettix_service_car_short_desc_use_list' );
			$this->service_car_short_desc_use_labels       = $this->get_option( 'nettix_service_car_short_desc_use_labels' );
			$this->service_car_short_desc_field_maker      = $this->get_option( 'nettix_service_car_short_desc_field_maker' );
			$this->service_car_short_desc_field_model      = $this->get_option( 'nettix_service_car_short_desc_field_model' );
			$this->service_car_short_desc_field_model_type = $this->get_option( 'nettix_service_car_short_desc_field_model_type' );
			$this->service_car_short_desc_field_year       = $this->get_option( 'nettix_service_car_short_desc_field_year' );
			$this->service_car_short_desc_field_kms        = $this->get_option( 'nettix_service_car_short_desc_field_kms' );
			$this->service_car_short_desc_field_fuel_type  = $this->get_option( 'nettix_service_car_short_desc_field_fuel_type' );
			$this->service_car_short_desc_field_location   = $this->get_option( 'nettix_service_car_short_desc_field_location' );

			// Nettikaravaani - settings
			$this->service_caravan_short_desc_use_list           = $this->get_option( 'nettix_service_caravan_short_desc_use_list' );
			$this->service_caravan_short_desc_use_labels         = $this->get_option( 'nettix_service_caravan_short_desc_use_labels' );
			$this->service_caravan_short_desc_field_vehicle_type = $this->get_option( 'nettix_service_caravan_short_desc_field_vehicle_type' );
			$this->service_caravan_short_desc_field_maker        = $this->get_option( 'nettix_service_caravan_short_desc_field_maker' );
			$this->service_caravan_short_desc_field_model        = $this->get_option( 'nettix_service_caravan_short_desc_field_model' );
			$this->service_caravan_short_desc_field_model_info   = $this->get_option( 'nettix_service_caravan_short_desc_field_model_info' );
			$this->service_caravan_short_desc_field_base         = $this->get_option( 'nettix_service_caravan_short_desc_field_base' );
			$this->service_caravan_short_desc_field_year         = $this->get_option( 'nettix_service_caravan_short_desc_field_year' );
			$this->service_caravan_short_desc_field_kms          = $this->get_option( 'nettix_service_caravan_short_desc_field_kms' );
			$this->service_caravan_short_desc_field_location     = $this->get_option( 'nettix_service_caravan_short_desc_field_location' );

			// Nettikone - settings
			$this->service_machine_short_desc_use_list         = $this->get_option( 'nettix_service_machine_short_desc_use_list' );
			$this->service_machine_short_desc_use_labels       = $this->get_option( 'nettix_service_machine_short_desc_use_labels' );
			$this->service_machine_short_desc_field_maker      = $this->get_option( 'nettix_service_machine_short_desc_field_maker' );
			$this->service_machine_short_desc_field_model      = $this->get_option( 'nettix_service_machine_short_desc_field_model' );
			$this->service_machine_short_desc_field_model_type = $this->get_option( 'nettix_service_machine_short_desc_field_model_type' );
			$this->service_machine_short_desc_field_fuel_type  = $this->get_option( 'nettix_service_machine_short_desc_field_fuel_type' );
			$this->service_machine_short_desc_field_year       = $this->get_option( 'nettix_service_machine_short_desc_field_year' );
			$this->service_machine_short_desc_field_kms        = $this->get_option( 'nettix_service_machine_short_desc_field_kms' );
			$this->service_machine_short_desc_field_hours      = $this->get_option( 'nettix_service_machine_short_desc_field_hours' );
			$this->service_machine_short_desc_field_location   = $this->get_option( 'nettix_service_machine_short_desc_field_location' );

			// Nettimoto - settings
			$this->service_bike_short_desc_use_list         = $this->get_option( 'nettix_service_bike_short_desc_use_list' );
			$this->service_bike_short_desc_use_labels       = $this->get_option( 'nettix_service_bike_short_desc_use_labels' );
			$this->service_bike_short_desc_field_maker      = $this->get_option( 'nettix_service_bike_short_desc_field_maker' );
			$this->service_bike_short_desc_field_model      = $this->get_option( 'nettix_service_bike_short_desc_field_model' );
			$this->service_bike_short_desc_field_model_type = $this->get_option( 'nettix_service_bike_short_desc_field_model_type' );
			$this->service_bike_short_desc_field_year       = $this->get_option( 'nettix_service_bike_short_desc_field_year' );
			$this->service_bike_short_desc_field_kms        = $this->get_option( 'nettix_service_bike_short_desc_field_kms' );
			$this->service_bike_short_desc_field_location   = $this->get_option( 'nettix_service_bike_short_desc_field_location' );

			// Nettivene - settings
			$this->service_boat_short_desc_use_list                         = $this->get_option( 'nettix_service_boat_short_desc_use_list' );
			$this->service_boat_short_desc_use_labels                       = $this->get_option( 'nettix_service_boat_short_desc_use_labels' );
			$this->service_boat_short_desc_field_boat_type                  = $this->get_option( 'nettix_service_boat_short_desc_field_boat_type' );
			$this->service_boat_short_desc_field_boat_sub_type              = $this->get_option( 'nettix_service_boat_short_desc_field_boat_sub_type' );
			$this->service_boat_short_desc_field_maker                      = $this->get_option( 'nettix_service_boat_short_desc_field_maker' );
			$this->service_boat_short_desc_field_model                      = $this->get_option( 'nettix_service_boat_short_desc_field_model' );
			$this->service_boat_short_desc_field_engine_maker               = $this->get_option( 'nettix_service_boat_short_desc_field_engine_maker' );
			$this->service_boat_short_desc_field_engine_model               = $this->get_option( 'nettix_service_boat_short_desc_field_engine_model' );
			$this->service_boat_short_desc_field_engine_model_specification = $this->get_option( 'nettix_service_boat_short_desc_field_engine_model_specification' );
			$this->service_boat_short_desc_field_year                       = $this->get_option( 'nettix_service_boat_short_desc_field_year' );
			$this->service_boat_short_desc_field_location                   = $this->get_option( 'nettix_service_boat_short_desc_field_location' );

			// Actions.
			add_action( 'woocommerce_update_options_integration_' . $this->id, [ $this, 'process_admin_options' ] );
		}

		/**
		 * Initialize integration settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = [
				// General settings fields
				'nettix_client_id'              => [
					'title'       => __( 'Client ID', 'wc-integration-to-nettix' ),
					'type'        => 'text',
					'description' => __( 'Enter with your Client ID provided by NettiX',
						'wc-integration-to-nettix' ),
					'desc_tip'    => true,
					'default'     => ''
				],
				'nettix_secret_id'              => [
					'title'       => __( 'Client Secret', 'wc-integration-to-nettix' ),
					'type'        => 'password',
					'description' => __( 'Enter with your Client Secret provided by NettiX',
						'wc-integration-to-nettix' ),
					'desc_tip'    => true,
					'default'     => ''
				],
				'nettix_user_ids'               => [
					'title'       => __( 'User IDs (store IDs)', 'wc-integration-to-nettix' ),
					'type'        => 'text',
					'description' => __( 'Comma separated list of user IDs (numerical store IDs) provided by NettiX',
						'wc-integration-to-nettix' ),
					'desc_tip'    => true,
					'default'     => ''
				],
				'nettix_service_car'            => [
					'title'       => __( 'NettiX Services', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Nettiauto', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Use NettiX service Nettiauto', 'wc-integration-to-nettix' ),
				],
				'nettix_service_caravan'        => [
					'type'        => 'checkbox',
					'label'       => __( 'Nettikaravaani', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Use NettiX service Nettikaravaani', 'wc-integration-to-nettix' ),
				],
				'nettix_service_machine'        => [
					'type'        => 'checkbox',
					'label'       => __( 'Nettikone', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Use NettiX service Nettikone', 'wc-integration-to-nettix' ),
				],
				'nettix_service_bike'           => [
					'type'        => 'checkbox',
					'label'       => __( 'Nettimoto', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Use NettiX service Nettimoto', 'wc-integration-to-nettix' ),
				],
				'nettix_service_boat'           => [
					'type'        => 'checkbox',
					'label'       => __( 'Nettivene', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Use NettiX service Nettivene', 'wc-integration-to-nettix' ),
				],
				'nettix_test_connection_button' => [
					'type'              => 'testconnbutton',
					'label'             => __( 'Test connection', 'wc-integration-to-nettix' ),
					'default'           => '',
					'description'       => __( 'Check that connection settings are correct. Testing logging-in to NettiX API.',
						'wc-integration-to-nettix' ),
					'desc_tip'          => true,
					'custom_attributes' => [
						'onclick' => 'testConnection()'
					]
				],
				'nettix_show_badge'  => [
					'title'       => __( 'Show badge on product card', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show Badge', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Defines if badge is shown in shop product card view' )
				],
				'nettix_badge_field' => [
					'type'        => 'select',
					'label'       => __( 'Field to show in badge', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'Info stored in select field is shown in badge on product card' ),
					'options'     => [
						'_wc_integration_to_nettix_vehicle_type'               => __( 'Vehicle type',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_boat_type'                  => __( 'Boat type',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_boat_sub_type'              => __( 'Boat sub-type',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_maker'                      => __( 'Maker',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_model'                      => __( 'Model',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_model_type'                 => __( 'Model type',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_base'                       => __( 'Base',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_kms'                        => __( 'Kilometers',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_hours'                      => __( 'Hours',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_fuel_type'                  => __( 'Fuel type',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_engine_maker'               => __( 'Engine maker',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_engine_model'               => __( 'Engine model',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_engine_model_specification' => __( 'Engine model specification',
							'wc-integration-to-nettix' ),
						'_wc_integration_to_nettix_location'                   => __( 'Location',
							'wc-integration-to-nettix' ),
					],
				],

				// Nettiauto settings fields

				'nettix_service_car_content_settings_section_header' => [
					'type'  => 'nettix_section_header',
					'label' => __( 'Nettiauto product content settings', 'wc-integration-to-nettix' )
				],
				'nettix_service_car_short_desc_use_list'             => [
					'title'       => __( 'Use list in product short description', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use list', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected fields are added as unordered list into short description using HTML',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_car_short_desc_use_labels'           => [
					'title'       => __( 'Show field labels before each field in short description',
						'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show labels', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected field names are shown before each field in short description',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_car_short_desc_field_maker'          => [
					'title'   => __( 'Fields to add to short description', 'wc-integration-to-nettix' ),
					'type'    => 'checkbox',
					'label'   => __( 'Maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_model'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_model_type'     => [
					'type'    => 'checkbox',
					'label'   => __( 'Model type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_year'           => [
					'type'    => 'checkbox',
					'label'   => __( 'Year', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_kms'            => [
					'type'    => 'checkbox',
					'label'   => __( 'Kilometers', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_fuel_type'      => [
					'type'    => 'checkbox',
					'label'   => __( 'Fuel type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_car_short_desc_field_location'       => [
					'type'    => 'checkbox',
					'label'   => __( 'Location', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],

				// Nettikaravaani settings fields

				'nettix_service_caravan_content_settings_section_header' => [
					'type'  => 'nettix_section_header',
					'label' => __( 'Nettikaravaani product content settings', 'wc-integration-to-nettix' )
				],
				'nettix_service_caravan_short_desc_use_list'             => [
					'title'       => __( 'Use list in product short description', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use list', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected fields are added as unordered list into short description using HTML',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_caravan_short_desc_use_labels'           => [
					'title'       => __( 'Show field labels before each field in short description',
						'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show labels', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected field names are shown before each field in short description',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_caravan_short_desc_field_vehicle_type'   => [
					'title'   => __( 'Fields to add to short description', 'wc-integration-to-nettix' ),
					'type'    => 'checkbox',
					'label'   => __( 'Vehicle type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_maker'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_model'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_model_info'     => [
					'type'    => 'checkbox',
					'label'   => __( 'Model information', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_base'           => [
					'type'    => 'checkbox',
					'label'   => __( 'Base', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_year'           => [
					'type'    => 'checkbox',
					'label'   => __( 'Year', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_kms'            => [
					'type'    => 'checkbox',
					'label'   => __( 'Kilometers', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_caravan_short_desc_field_location'       => [
					'type'    => 'checkbox',
					'label'   => __( 'Location', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],

				// Nettikone settings fields

				'nettix_service_machine_content_settings_section_header' => [
					'type'  => 'nettix_section_header',
					'label' => __( 'Nettikone product content settings', 'wc-integration-to-nettix' )
				],
				'nettix_service_machine_short_desc_use_list'             => [
					'title'       => __( 'Use list in product short description', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use list', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected fields are added as unordered list into short description using HTML',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_machine_short_desc_use_labels'           => [
					'title'       => __( 'Show field labels before each field in short description',
						'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show labels', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected field names are shown before each field in short description',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_machine_short_desc_field_maker'          => [
					'title'   => __( 'Fields to add to short description', 'wc-integration-to-nettix' ),
					'type'    => 'checkbox',
					'label'   => __( 'Maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_model'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_model_type'     => [
					'type'    => 'checkbox',
					'label'   => __( 'Model type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_fuel_type'      => [
					'type'    => 'checkbox',
					'label'   => __( 'Fuel type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_year'           => [
					'type'    => 'checkbox',
					'label'   => __( 'Year', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_kms'            => [
					'type'    => 'checkbox',
					'label'   => __( 'Kilometers', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_hours'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Hours', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_machine_short_desc_field_location'       => [
					'type'    => 'checkbox',
					'label'   => __( 'Location', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],

				// Nettimoto settings fields

				'nettix_service_bike_content_settings_section_header' => [
					'type'  => 'nettix_section_header',
					'label' => __( 'Nettimoto product content settings', 'wc-integration-to-nettix' )
				],
				'nettix_service_bike_short_desc_use_list'             => [
					'title'       => __( 'Use list in product short description', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use list', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected fields are added as unordered list into short description using HTML',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_bike_short_desc_use_labels'           => [
					'title'       => __( 'Show field labels before each field in short description',
						'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show labels', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected field names are shown before each field in short description',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_bike_short_desc_field_maker'          => [
					'title'   => __( 'Fields to add to short description', 'wc-integration-to-nettix' ),
					'type'    => 'checkbox',
					'label'   => __( 'Maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_bike_short_desc_field_model'          => [
					'type'    => 'checkbox',
					'label'   => __( 'Model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_bike_short_desc_field_model_type'     => [
					'type'    => 'checkbox',
					'label'   => __( 'Model type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_bike_short_desc_field_year'           => [
					'type'    => 'checkbox',
					'label'   => __( 'Year', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_bike_short_desc_field_kms'            => [
					'type'    => 'checkbox',
					'label'   => __( 'Kilometers', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_bike_short_desc_field_location'       => [
					'type'    => 'checkbox',
					'label'   => __( 'Location', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],

				// Nettivene settings fields

				'nettix_service_boat_content_settings_section_header'             => [
					'type'  => 'nettix_section_header',
					'label' => __( 'Nettivene product content settings', 'wc-integration-to-nettix' )
				],
				'nettix_service_boat_short_desc_use_list'                         => [
					'title'       => __( 'Use list in product short description', 'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Use list', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected fields are added as unordered list into short description using HTML',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_boat_short_desc_use_labels'                       => [
					'title'       => __( 'Show field labels before each field in short description',
						'wc-integration-to-nettix' ),
					'type'        => 'checkbox',
					'label'       => __( 'Show labels', 'wc-integration-to-nettix' ),
					'default'     => 'no',
					'description' => __( 'When selected field names are shown before each field in short description',
						'wc-integration-to-nettix' ),
				],
				'nettix_service_boat_short_desc_field_boat_type'                  => [
					'title'   => __( 'Fields to add to short description', 'wc-integration-to-nettix' ),
					'type'    => 'checkbox',
					'label'   => __( 'Boat type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_boat_sub_type'              => [
					'type'    => 'checkbox',
					'label'   => __( 'Boat sub-type', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_maker'                      => [
					'type'    => 'checkbox',
					'label'   => __( 'Maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_model'                      => [
					'type'    => 'checkbox',
					'label'   => __( 'Model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_engine_maker'               => [
					'type'    => 'checkbox',
					'label'   => __( 'Engine maker', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_engine_model'               => [
					'type'    => 'checkbox',
					'label'   => __( 'Engine model', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_engine_model_specification' => [
					'type'    => 'checkbox',
					'label'   => __( 'Engine model specification', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_year'                       => [
					'type'    => 'checkbox',
					'label'   => __( 'Year', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
				'nettix_service_boat_short_desc_field_location'                   => [
					'type'    => 'checkbox',
					'label'   => __( 'Location', 'wc-integration-to-nettix' ),
					'default' => 'no',
				],
			];
		}

		public function generate_testconnbutton_html( $key, $data ) {
			$field    = $this->plugin_id . $this->id . '_' . $key;
			$defaults = [
				'class'             => 'button-secondary',
				'css'               => '',
				'custom_attributes' => [],
				'desc_tip'          => false,
				'description'       => '',
				'label'             => '',
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
            <tr>
                <th scope="row" class="titledesc">
                    <label for="<?php
					echo esc_attr( $field ); ?>"><?php
						echo wp_kses_post( $data['label'] ); ?></label>
					<?php
					echo $this->get_tooltip_html( $data ); ?>
                </th>
                <td class="forminp">
                    <fieldset>
                        <legend class="screen-reader-text"><span><?php
								echo wp_kses_post( $data['label'] ); ?></span></legend>
                        <button class="<?php
						echo esc_attr( $data['class'] ); ?>" type="button" name="<?php
						echo esc_attr( $field ); ?>" id="<?php
						echo esc_attr( $field ); ?>" style="<?php
						echo esc_attr( $data['css'] ); ?>" <?php
						echo $this->get_custom_attribute_html( $data ); ?>><?php
							echo wp_kses_post( $data['label'] ); ?></button>
                        <span id="wc-nettix-integration-test-connection-spinner" class="spinner"
                              style="float: none !important"></span><span
                                id="wc-nettix-integration-test-connection-result"></span>
						<?php
						echo $this->get_description_html( $data ); ?>
                    </fieldset>
                </td>
            </tr>
			<?php
			return ob_get_clean();
		}

		public function generate_nettix_section_header_html( $key, $data ) {
			$field    = $this->plugin_id . $this->id . '_' . $key;
			$defaults = [
				'class'             => '',
				'css'               => '',
				'custom_attributes' => [],
				'desc_tip'          => false,
				'description'       => '',
				'label'             => '',
			];

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			?>
            <tr>
                <td colspan="2">
                    <h3 class="<?php
					echo esc_attr( $data['class'] ); ?>"><?php
						echo wp_kses_post( $data['label'] ); ?></h3>
                </td>
            </tr>
			<?php
			return ob_get_clean();
		}
	}
}