<?php

namespace WC_Integration_NettiX\NettiX_Client;

use Exception;
use WC_Integration_NettiX\Plugin\Plugin_Settings;

/**
 * Class Base_Client. Basic client for connecting to NettiX API.
 * Clients for each NettiX service extends this client class.
 */
abstract class Base_Client {
	protected const API_BASE_URL = 'https://api.nettix.fi/';

	protected string $api_url = '';
	protected Plugin_Settings $settings;
	protected Authentication $authentication;

	/**
	 * BaseClient constructor.
	 *
	 * @param Plugin_Settings $settings
	 */
	public function __construct( Plugin_Settings $settings ) {
		$this->settings       = $settings;
		$this->authentication = new Authentication( $this->settings );
	}

	/**
	 * Handles search criterias for each client type.
	 *
	 * @param array $search_criteria
	 *
	 * @return array
	 */
	protected function handle_search_criteria( array $search_criteria ): array {
		$handled_search_criteria = [];

		foreach ( $search_criteria as $key => $value ) {
			$key                             = $this->get_mapping_for_search_param_key( $key );
			$handled_search_criteria[ $key ] = $this->sanitize_value( $key, $value );
		}

		$this->add_user_id_criteria( $handled_search_criteria );

		return $handled_search_criteria;
	}

	/**
	 * Returns array of correct attribute names based on the ones butchered by WP.
	 * Array key is the butchered attribute name and value is the correct attribute name.
	 *
	 * @return string[]
	 */
	abstract protected function get_search_criteria_key_mappings(): array;

	/**
	 * Sanitizes the search criteria value.
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	abstract protected function sanitize_value( string $key, $value );

	/**
	 * General add method of NettiX user IDs to search criterias.
	 *
	 * @param array $search_criteria
	 */
	protected function add_user_id_criteria( array &$search_criteria ) {
		$search_criteria['userId'] = $this->get_user_ids_array();
	}

	/**
	 * @return array
	 */
	protected function get_user_ids_array(): array {
		$user_ids = sanitize_text_field( $this->settings->get_user_ids() );

		$trim_user_id   = function ( &$user_id, $key ) {
			$user_id = trim( $user_id );
		};
		$user_ids_array = explode( ',', $user_ids );

		array_walk( $user_ids_array, $trim_user_id );

		return $user_ids_array;
	}

	/**
	 * @param array $search_criteria
	 *
	 * @return array
	 * @throws Exception
	 */
	public function search( array $search_criteria ): array {
		try {
			if ( ! $this->api_url ) {
				throw new Exception( "API URL not found", 1 );
			}

			$api_method_url = $this->api_url . 'search';

			return $this->do_nettix_request( $api_method_url, $search_criteria );
		} catch ( Exception $e ) {
            error_log("NettiX API: {$e->getMessage()}");
			return [];
		}
	}

	/**
	 * @param string $api_url
	 * @param array $search_criteria
	 *
	 * @return array
	 * @throws Exception
	 */
	private function do_nettix_request( string $api_url, array $search_criteria ): array {
		$search_criteria = $this->handle_search_criteria( $search_criteria );

		if ( ! $api_url ) {
			throw new Exception( "API URL not found", 1 );
		}

		if ( ! $auth_token = $this->authentication->get_auth_token() ) {
			throw new Exception( "Authentication failed.", 2 );
		}

		$args = [
			'body'        => $search_criteria,
			'timeout'     => 30,
			'redirection' => 5,
			'httpVersion' => '1.1',
			'blocking'    => true,
			'headers'     => [ 'X-Access-Token' => $auth_token ],
			'cookies'     => [],
		];

		$response           = wp_remote_get( $api_url, $args );
		$http_response_code = wp_remote_retrieve_response_code( $response );

		if ( is_int( $http_response_code ) && $http_response_code != 200 ) {
			$this->handle_http_error_code( $http_response_code );
		} elseif ( ! $http_response_code ) {
			throw new Exception( "Failed to get HTTP response code from API", 8 );
		}

		if ( ! $response_array = json_decode( wp_remote_retrieve_body( $response ), true ) ) {
			throw new Exception( "Could not parse response JSON to array", 7 );
		}

		return $response_array;
	}

    /**
     * Does NettiX API request to fetch count of ads for specified search criterias.
     *
     * @param array $search_criteria
     *
     * @return int
     * @throws Exception
     */
	public function get_ad_count( array $search_criteria ): int {
        if ( ! $this->api_url ) {
            throw new Exception( "API URL not found", 1 );
        }

        $api_method_url = $this->api_url . 'search-count';

        $responseArray = $this->do_nettix_request( $api_method_url, $search_criteria );

        return (int) $responseArray['total'] ?? 0;
	}

	/**
	 * Handles NettiX API request errors.
	 *
	 * @param int $http_response_code
	 *
	 * @throws Exception
	 */
	protected function handle_http_error_code( int $http_response_code ) {
		switch ( $http_response_code ) {
			case 401:
				throw new Exception( $http_response_code . " Unauthorized", 3 );
			case 403:
				throw new Exception( $http_response_code . " Forbidden to use API", 4 );
			case 404:
				throw new Exception( $http_response_code . " Not found", 5 );
			default:
				throw new Exception( sprintf( "API method call failed. HTTP Response code: %s", $http_response_code ),
					6 );
		}
	}

	/**
	 * Transforms attribute name butchered by WP to the correct attribute name.
	 * Needed for queries done using shortcodes (coming feature).
	 *
	 * @param string $key
	 *
	 * @return string|null
	 */
	protected function get_mapping_for_search_param_key( string $key ): ?string {
		$mappings = $this->get_search_criteria_key_mappings();
		if ( ! $new_key = $mappings[ $key ] ?? null ) {
			return $key;
		}

		return $new_key;
	}
}