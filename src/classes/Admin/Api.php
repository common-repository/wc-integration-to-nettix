<?php

namespace WC_Integration_NettiX\Admin;

use Exception;
use WC_Integration_NettiX\NettiX_Client\Authentication;
use WC_Integration_NettiX\NettiX_Client\Nettiauto_Client;
use WC_Integration_NettiX\NettiX_Client\Nettikaravaani_Client;
use WC_Integration_NettiX\NettiX_Client\Nettikone_Client;
use WC_Integration_NettiX\NettiX_Client\Nettimoto_Client;
use WC_Integration_NettiX\NettiX_Client\Nettivene_Client;
use WC_Integration_NettiX\Plugin\Fetcher;
use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Api {
	public function test_connection() {
		if ( ! (
			is_user_logged_in() && (
				current_user_can( 'administrator' )
				|| current_user_can( 'shop_manager' )
			)
		) ) {
			wp_send_json( [
				'success'      => false,
				'shouldNotify' => false,
				'messageType'  => 'error',
				'message'      => __( 'Not allowed.', 'wc-integration-to-nettix' )
			] );
			wp_die();
		}

		$client_id       = sanitize_text_field( $_POST['client_id'] );
		$client_secret   = sanitize_text_field( $_POST['client_secret'] );
		$user_ids        = sanitize_text_field( $_POST['user_ids'] );
		$service_car     = filter_var( $_POST['service_car'], FILTER_SANITIZE_NUMBER_INT );
		$service_bike    = filter_var( $_POST['service_bike'], FILTER_SANITIZE_NUMBER_INT );
		$service_boat    = filter_var( $_POST['service_boat'], FILTER_SANITIZE_NUMBER_INT );
		$service_machine = filter_var( $_POST['service_machine'], FILTER_SANITIZE_NUMBER_INT );
		$service_caravan = filter_var( $_POST['service_caravan'], FILTER_SANITIZE_NUMBER_INT );

		if ( ! $client_id || ! $client_secret ) {
			wp_send_json( [
				'success'      => true,
				'shouldNotify' => true,
				'messageType'  => 'error',
				'message'      => __( 'Logging-in to API failed. Check Client ID and Client Secret.',
					'wc-integration-to-nettix' ),
				'data'         => []
			] );
			wp_die();
		}

		$auth     = new Authentication( new Plugin_Settings() );

		try {
			$response = $auth->send_authentication_request( $client_id, $client_secret );
		} catch ( Exception $e ) {
			wp_send_json( [
				'success'      => true,
				'shouldNotify' => true,
				'messageType'  => 'error',
				'message'      => sprintf( __( 'Logging-in to API failed due to error. Error: %s',
					'wc-integration-to-nettix' ),
					$e->getMessage() ),
				'data'         => []
			] );
			wp_die();
		}

		$success = true;

		if ( ! ( $response['expires_in'] ?? null ) ) {
			$success = false;
		}

		if ( ! ( $response['access_token'] ?? null ) ) {
			$success = false;
		}

		if ( $success && ! $user_ids ) {
			wp_send_json( [
				'success'      => true,
				'shouldNotify' => true,
				'messageType'  => 'error',
				'message'      => __( 'Logging-in to API succeeded, but User IDs are not defined.',
					'wc-integration-to-nettix' ),
				'data'         => []
			] );
			wp_die();
		} elseif ( ! $success ) {
			wp_send_json( [
				'success'      => true,
				'shouldNotify' => true,
				'messageType'  => 'error',
				'message'      => __( 'Logging-in to API failed. Check Client ID and Client Secret.',
					'wc-integration-to-nettix' ),
				'data'         => []
			] );
			wp_die();
		}

		$settings = new Plugin_Settings(
			[
				'nettix_client_id'       => $client_id,
				'nettix_secret_id'       => $client_secret,
				'nettix_user_ids'        => $user_ids,
				'nettix_service_bike'    => (string) $service_bike,
				'nettix_service_boat'    => (string) $service_boat,
				'nettix_service_car'     => (string) $service_car,
				'nettix_service_machine' => (string) $service_machine,
				'nettix_service_caravan' => (string) $service_caravan,
			]
		);


		if ( $service_car ) {
			$client_car = new Nettiauto_Client( $settings );

			try {
				if ( ! $client_car->get_ad_count( [] ) ) {
					wp_send_json(
						[
							'success'      => true,
							'shouldNotify' => true,
							'messageType'  => 'error',
							'message'      => __(
								'Logging-in to API succeeded, but no ads found in Nettiauto.',
								'wc-integration-to-nettix'
							),
							'data'         => []
						]
					);
					wp_die();
				}
			} catch ( Exception $e ) {
				wp_send_json(
					[
						'success'      => true,
						'shouldNotify' => true,
						'messageType'  => 'error',
						'message'      => sprintf(
							__(
								'Logging-in to API succeeded, but error occurred while fetching ads from Nettiauto. Error: %s',
								'wc-integration-to-nettix'
							),
							$e->getMessage()
						),
						'data'         => []
					]
				);
				wp_die();
			}
		}

		if ( $service_bike ) {
			$client_bike = new Nettimoto_Client( $settings );

			try {
				if ( ! $client_bike->get_ad_count( [] ) ) {
					wp_send_json(
						[
							'success'      => true,
							'shouldNotify' => true,
							'messageType'  => 'error',
							'message'      => __(
								'Logging-in to API succeeded, but no ads found in Nettimoto.',
								'wc-integration-to-nettix'
							),
							'data'         => []
						]
					);
					wp_die();
				}
			} catch ( Exception $e ) {
				wp_send_json(
					[
						'success'      => true,
						'shouldNotify' => true,
						'messageType'  => 'error',
						'message'      => sprintf(
							__(
								'Logging-in to API succeeded, but error occurred while fetching ads from Nettimoto. Error: %s',
								'wc-integration-to-nettix'
							),
							$e->getMessage()
						),
						'data'         => []
					]
				);
				wp_die();
			}
		}

		if ( $service_boat ) {
			$client_boat = new Nettivene_Client( $settings );

			try {
				if ( ! $client_boat->get_ad_count( [] ) ) {
					wp_send_json(
						[
							'success'      => true,
							'shouldNotify' => true,
							'messageType'  => 'error',
							'message'      => __(
								'Logging-in to API succeeded, but no ads found in Nettivene.',
								'wc-integration-to-nettix'
							),
							'data'         => []
						]
					);
					wp_die();
				}
			} catch ( Exception $e ) {
				wp_send_json(
					[
						'success'      => true,
						'shouldNotify' => true,
						'messageType'  => 'error',
						'message'      => sprintf(
							__(
								'Logging-in to API succeeded, but error occurred while fetching ads from Nettivene. Error: %s',
								'wc-integration-to-nettix'
							),
							$e->getMessage()
						),
						'data'         => []
					]
				);
				wp_die();
			}
		}

		if ( $service_machine ) {
			$client_machine = new Nettikone_Client( $settings );

			try {
				if ( ! $client_machine->get_ad_count( [] ) ) {
					wp_send_json(
						[
							'success'      => true,
							'shouldNotify' => true,
							'messageType'  => 'error',
							'message'      => __(
								'Logging-in to API succeeded, but no ads found in Nettikone.',
								'wc-integration-to-nettix'
							),
							'data'         => []
						]
					);
					wp_die();
				}
			} catch ( Exception $e ) {
				wp_send_json(
					[
						'success'      => true,
						'shouldNotify' => true,
						'messageType'  => 'error',
						'message'      => sprintf(
							__(
								'Logging-in to API succeeded, but error occurred while fetching ads from Nettikone. Error: %s',
								'wc-integration-to-nettix'
							),
							$e->getMessage()
						),
						'data'         => []
					]
				);
				wp_die();
			}
		}

		if ( $service_caravan ) {
			$client_caravan = new Nettikaravaani_Client( $settings );

			try {
				if ( ! $client_caravan->get_ad_count( [] ) ) {
					wp_send_json(
						[
							'success'      => true,
							'shouldNotify' => true,
							'messageType'  => 'error',
							'message'      => __(
								'Logging-in to API succeeded, but no ads found in Nettikaravaani.',
								'wc-integration-to-nettix'
							),
							'data'         => []
						]
					);
					wp_die();
				}
			} catch ( Exception $e ) {
				wp_send_json(
					[
						'success'      => true,
						'shouldNotify' => true,
						'messageType'  => 'error',
						'message'      => sprintf(
							__(
								'Logging-in to API succeeded, but error occurred while fetching ads from Nettikaravaani. Error: %s',
								'wc-integration-to-nettix'
							),
							$e->getMessage()
						),
						'data'         => []
					]
				);
				wp_die();
			}
		}

		wp_send_json( [
			'success'      => true,
			'shouldNotify' => true,
			'messageType'  => 'notice',
			'message'      => __( 'Success!', 'wc-integration-to-nettix' ),
			'data'         => []
		] );
		wp_die();
	}
}