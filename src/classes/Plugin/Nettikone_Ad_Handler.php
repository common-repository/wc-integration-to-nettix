<?php

namespace WC_Integration_NettiX\Plugin;

use Exception;
use WP_Term;

class Nettikone_Ad_Handler extends Ad_Handler {
	/**
	 * @return WP_Term
	 * @throws Exception
	 */
	public function get_main_category(): WP_Term {
		return $this->get_category( 'nettikone' );
	}

	/**
	 * @param array $ad_response_data
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function populate_wc_data( array &$ad_response_data ): void {
		if ( ! sanitize_text_field( $ad_response_data['id'] ?? '' ) ) {
			throw new Exception( "No id in ad data" );
		}

		if ( ! esc_url_raw( $ad_response_data['adUrl'] ?? '' ) ) {
			throw new Exception( "No ad URL" );
		}

		if ( ! $maker = sanitize_text_field( $ad_response_data['make']['name'] ?? '' ) ) {
			throw new Exception( "No maker name" );
		}

		$main_category = $this->get_main_category();
		$maker_category = $this->get_maker_category($maker);

		$wc_data = [
			'slug_prefix' => 'nettikone',
			'category_ids' => [ $main_category->term_id, $maker_category->term_id ],
			'tag_ids' => [],
		];

		if ( $maker_tag = $this->get_tag( $maker ) ) {
			$wc_data['tag_ids'][] = $maker_tag->term_id;
		}

		$seller_name_tag = $this->get_tag( sanitize_text_field( $ad_response_data['userName'] ?? '' ) );

		if ( $seller_name_tag ) {
			$wc_data['tag_ids'][] = $seller_name_tag->term_id;
		}

		if ( $town = sanitize_text_field( $ad_response_data['town']['fi'] ?? '' ) ) {
			if ( $town_tag = $this->get_tag( $town ) ) {
				$wc_data['tag_ids'][] = $town_tag->term_id;
			}
		}

		$model = sanitize_text_field( $ad_response_data['model']['name'] ?? '' );

		if ( $model === '' ) {
			$model = sanitize_text_field( $ad_response_data['model'] ?? '' );
		}

		$model_type = sanitize_text_field( $ad_response_data['modelTypeName'] ?? '' );

		$fuel_type = sanitize_text_field( $ad_response_data['fuelType']['fi'] ?? '' );

		$year = (int) sanitize_text_field( $ad_response_data['year'] ?? 0 );

		$kilometers = (int) sanitize_text_field( $ad_response_data['kilometers'] ?? 0 );

		$hours = (int) sanitize_text_field( $ad_response_data['hours'] ?? 0 );

		$title = $maker;

		if ( $model !== '' ) {
			$title .= ' ' . $model;
		}

		if ( $model_type !== '' ) {
			$title .= ' ' . $model_type;
		}

		if ( $year !== '' ) {
			$title .= ' ' . $year;
		}

		$wc_data['title'] = $title;
		$wc_data['short_description'] = $this->create_short_description_content( $maker,
			$model,
			$model_type,
			$fuel_type,
			$year,
			$kilometers,
			$hours,
			$town );

		$wc_data['custom_fields'] = [
			'_wc_integration_to_nettix_maker'      => $maker,
			'_wc_integration_to_nettix_model'      => $model,
			'_wc_integration_to_nettix_model_type' => $model_type,
			'_wc_integration_to_nettix_year'       => $year,
			'_wc_integration_to_nettix_kms'        => $kilometers,
			'_wc_integration_to_nettix_hours'      => $hours,
			'_wc_integration_to_nettix_fuel_type'  => $fuel_type,
			'_wc_integration_to_nettix_location'   => $town,
		];

		$ad_response_data['wc_data'] = $wc_data;
	}

	private function create_short_description_content(
		string $maker,
		string $model,
		string $model_type,
		string $fuel_type,
		int $year,
		int $kilometers,
		int $hours,
		string $location
	): string {
		$ret = "";
		if ( $this->settings->get_service_machine_short_desc_use_list() == 'yes' ) {
			$ret .= "<ul>\n";

			if ( $this->settings->get_service_machine_short_desc_field_maker() == 'yes' && $maker ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Merkki ja malli: ";
				}
				$ret .= $maker;
				if ( $this->settings->get_service_machine_short_desc_field_model() == 'yes' && $model ) {
					$ret .= " {$model}";
				}
				if ( $this->settings->get_service_machine_short_desc_field_model_type() == 'yes' && $model_type ) {
					$ret .= " {$model_type}";
				}
				$ret .= "</li>\n";
			}

			if ( $this->settings->get_service_machine_short_desc_field_fuel_type() == 'yes' && $fuel_type ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Käyttövoima: ";
				}
				$ret .= "{$fuel_type}</li>\n";
			}

			if ( $this->settings->get_service_machine_short_desc_field_year() == 'yes' && $year > 0 ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Vuosimalli: ";
				}
				$ret .= "{$year}</li>\n";
			}

			if ( $this->settings->get_service_machine_short_desc_field_kms() == 'yes' && $kilometers > 0 ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Mittarilukema: ";
				}
				$ret .= "{$this->format_kilometers($kilometers)}</li>\n";
			}

			if ( $this->settings->get_service_machine_short_desc_field_hours() == 'yes' && $hours > 0 ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Käyttötunnit: ";
				}
				$ret .= "{$this->format_hours($hours)}</li>\n";
			}

			if ( $this->settings->get_service_machine_short_desc_field_location() == 'yes' && $location > 0 ) {
				$ret .= "<li>";
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Sijainti: ";
				}
				$ret .= "{$location}</li>\n";
			}

			$ret .= "</ul>\n";
		} else {
			$added = false;
			if ( $this->settings->get_service_machine_short_desc_field_maker() == 'yes' && $maker ) {
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Merkki ja malli: ";
				}
				$ret   .= $maker;
				if ( $this->settings->get_service_machine_short_desc_field_model() == 'yes' && $model ) {
					$ret .= " {$model}";
				}
				if ( $this->settings->get_service_machine_short_desc_field_model_type() == 'yes' && $model_type ) {
					$ret .= " {$model_type}";
				}
				$added = true;
			}

			if ( $this->settings->get_service_machine_short_desc_field_fuel_type() == 'yes' && $fuel_type ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Käyttövoima: ";
				}
				$ret .= $fuel_type;
				$added = true;
			}

			if ( $this->settings->get_service_machine_short_desc_field_year() == 'yes' && $year > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Vuosimalli: ";
				}
				$ret   .= $year;
				$added = true;
			}

			if ( $this->settings->get_service_machine_short_desc_field_kms() == 'yes' && $kilometers > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Mittarilukema: ";
				}
				$ret .= $this->format_kilometers( $kilometers );
				$added = true;
			}

			if ( $this->settings->get_service_machine_short_desc_field_kms() == 'yes' && $hours > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Käyttötunnit: ";
				}
				$ret .= $this->format_hours( $hours );
				$added = true;
			}

			if ( $this->settings->get_service_machine_short_desc_field_location() == 'yes' && $location > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ($this->settings->get_service_machine_short_desc_use_labels() == 'yes') {
					$ret .= "Sijainti: ";
				}
				$ret   .= $location;
			}
		}

		return $ret;
	}
}