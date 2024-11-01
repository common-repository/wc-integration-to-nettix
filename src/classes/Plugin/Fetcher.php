<?php

namespace WC_Integration_NettiX\Plugin;

use Exception;
use WC_Integration_NettiX\NettiX_Client\Base_Client;
use WC_Integration_NettiX\NettiX_Client\Nettiauto_Client;
use WC_Integration_NettiX\NettiX_Client\Nettikaravaani_Client;
use WC_Integration_NettiX\NettiX_Client\Nettikone_Client;
use WC_Integration_NettiX\NettiX_Client\Nettimoto_Client;
use WC_Integration_NettiX\NettiX_Client\Nettivene_Client;

class Fetcher {
	private Plugin_Settings $settings;
	private Database_Settings $database_settings;

	public function __construct(
		Plugin_Settings $settings
	) {
		$this->settings              = $settings;
		$this->database_settings     = new Database_Settings();
	}

	/**
	 * Fetches all ads from NettiX. Loops through all enabled services.
	 */
	public function fetch() {
		if ( $this->settings->get_service_bike() == 'yes' ) {
			$client  = new Nettimoto_Client( $this->settings );
			$handler = new Nettimoto_Ad_Handler( $this->settings );
			$this->fetch_ads( $client, $handler );
		}

		if ( $this->settings->get_service_boat() == 'yes' ) {
			$client  = new Nettivene_Client( $this->settings );
			$handler = new Nettivene_Ad_Handler( $this->settings );
			$this->fetch_ads( $client, $handler );
		}

		if ( $this->settings->get_service_car() == 'yes' ) {
			$client  = new Nettiauto_Client( $this->settings );
			$handler = new Nettiauto_Ad_Handler( $this->settings );
			$this->fetch_ads( $client, $handler );
		}

		if ( $this->settings->get_service_machine() == 'yes' ) {
			$client  = new Nettikone_Client( $this->settings );
			$handler = new Nettikone_Ad_Handler( $this->settings );
			$this->fetch_ads( $client, $handler );
		}

		if ( $this->settings->get_service_caravan() == 'yes' ) {
			$client  = new Nettikaravaani_Client( $this->settings );
			$handler = new Nettikaravaani_Ad_Handler( $this->settings );
			$this->fetch_ads( $client, $handler );
		}
	}

	/**
	 * Start fetching and handling ads of one NettiX service client has access to.
	 *
	 * @param Base_Client $client
	 * @param Ad_Handler $handler
	 *
	 * @return void
	 */
	private function fetch_ads( Base_Client $client, Ad_Handler $handler ): void {
		$criteria = [];

		try {
			if ( $ad_count = $client->get_ad_count( $criteria ) ) {
				$ads = [];
				$page_count = ceil( $ad_count / 100 );

				for ( $i = 1; $i < $page_count + 1; $i ++ ) {
					$criteria['rows'] = 100;
					$criteria['page'] = $i;

					foreach ( $client->search( $criteria ) as $ad) {
						$nettix_id = $ad['id'] ?? '';

						if ( ! $nettix_id ) {
							continue;
						}

						if ( ! array_key_exists($nettix_id, $ads) ) {
							$ads[$nettix_id] = $ad;
						}
					}
				}

				$handler->handle( $ads );
			}
		} catch ( Exception $e ) {
			error_log( "NettiX API: {$e->getMessage()}" );
		}
	}
}