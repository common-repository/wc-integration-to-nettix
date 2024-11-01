<?php

namespace WC_Integration_NettiX\NettiX_Client;

use DateTime;
use Exception;
use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Authentication {
	private const TOKEN_TRANSIENT_NAME = 'nettix-wc-integration-token-storage';

	private string $auth_token = '';
	private string $refresh_token = '';
	private string $token_type = '';
	private ?int $expires_in;
	private ?DateTime $expire_time;

	private string $nettix_token_method_url = "https://auth.nettix.fi/oauth2/token";

	private Plugin_Settings $settings;

	public function __construct( Plugin_Settings $settings ) {
		$this->settings = $settings;
		$this->populate_from_cache();
	}

	private function cache_auth_token(): void {
		$token_data = [
			'auth_token'    => $this->auth_token,
			'refresh_token' => $this->refresh_token,
			'token_type'    => $this->token_type,
			'expires_in'    => $this->expires_in,
			'expire_time'   => $this->expire_time
		];

		set_transient(self::TOKEN_TRANSIENT_NAME, $token_data, 86350);
	}

	private function populate_from_cache(): void {
		if ( ! $token_data = get_transient(self::TOKEN_TRANSIENT_NAME) ) {
			return;
		}

		$this->auth_token    = sanitize_text_field( $token_data['auth_token'] ?? '' );
		$this->refresh_token = sanitize_text_field( $token_data['refresh_token'] ?? '' );
		$this->token_type    = sanitize_text_field( $token_data['token_type'] ?? '' );
		$this->expires_in    = (int) $token_data['expires_in'] ?? null;

		$expire_time = $token_data['expire_time'] ?? null;
		if ( $expire_time instanceof DateTime ) {
			$this->expire_time = $expire_time;
		}
	}

	/**
	 * Does authentication to NettiX API using client ID and client secret provided by NettiX.
	 * Caches authentication token for future use.
	 *
	 * @return bool
	 */
	public function authenticate(): bool {
		if ( ! $client_id = sanitize_text_field( $this->settings->get_client_id() ) ) {
			return false;
		}

		if ( ! $client_secret = sanitize_text_field( $this->settings->get_client_secret() ) ) {
			return false;
		}

		try {
			$http_response_array = $this->send_authentication_request( $client_id, $client_secret );
		} catch ( Exception $e ) {
			error_log( "NettiX API: " . $e->getMessage() );
			return false;
		}

		if ( ! $this->expires_in = (int) $http_response_array['expires_in'] ?? null ) {
			return false;
		}

		if ( ! $this->auth_token = sanitize_text_field( $http_response_array['access_token'] ?? '' ) ) {
			return false;
		}

		if ( ! $this->token_type = sanitize_text_field( $http_response_array['token_type'] ?? '' ) ) {
			return false;
		}

		try {
			$this->expire_time = ( new DateTime() )->modify( '+' . $this->expires_in . ' seconds' );
		} catch ( Exception $e ) {
			return false;
		}

		$this->cache_auth_token();

		return true;
	}

	/**
	 * @param string $client_id
	 * @param string $client_secret
	 *
	 * @return array
	 * @throws Exception
	 */
	public function send_authentication_request(string $client_id, string $client_secret): array {
		$args = [
			'body'        => [
				'grant_type'    => 'client_credentials',
				'client_id'     => $client_id,
				'client_secret' => $client_secret
			],
			'timeout'     => 30,
			'redirection' => 5,
			'httpVersion' => '1.1',
			'blocking'    => true,
			'headers'     => [],
			'cookies'     => [],
		];

		$response = wp_remote_post( $this->nettix_token_method_url, $args );

		$http_response_code = wp_remote_retrieve_response_code( $response );

		if ( is_int($http_response_code) && $http_response_code != 200 ) {
			return [];
		} else if (!$http_response_code) {
			throw new Exception("Failed to get HTTP response code from Authentication API", 8);
		}

		$http_response_content = wp_remote_retrieve_body( $response );

		return json_decode( $http_response_content, true );
	}

	/**
	 * @return string
	 */
	public function get_auth_token(): string {
		if ( ! $this->auth_token ) {
			if ( ! $this->authenticate() ) {
				return '';
			}
		}

		return $this->auth_token;
	}
}