<?php

namespace WC_Integration_NettiX\Plugin;

use Exception;
use WP_Term;

class Nettivene_Ad_Handler extends Ad_Handler {
	/**
	 * @return WP_Term
	 * @throws Exception
	 */
	public function get_main_category(): WP_Term {
		return $this->get_category( 'nettivene' );
	}

	/**
	 * @param array $ad_response_data
	 *
	 * @return void
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
		$maker_category = $this->get_maker_category( $maker );

		$wc_data = [
			'slug_prefix' => 'nettivene',
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

		if ( $boat_type = sanitize_text_field( $ad_response_data['boatType']['fi'] ?? '' ) ) {
			if ( $boat_type_tag = $this->get_tag( $boat_type ) ) {
				$wc_data['tag_ids'][] = $boat_type_tag->term_id;
			}
		}

		$boat_sub_type = '';
		if ( $boat_type && $boat_sub_type = sanitize_text_field( $ad_response_data['subType']['fi'] ?? '' ) ) {
			if ( $boat_sub_type_tag = $this->get_tag( $boat_sub_type ) ) {
				$wc_data['tag_ids'][] = $boat_sub_type_tag->term_id;
			}
		}

		$model = sanitize_text_field( $ad_response_data['model']['name'] ?? '' );

		if ( $model === '' ) {
			$model = sanitize_text_field( $ad_response_data['model'] ?? '' );
		}

		$engine_maker               = sanitize_text_field( $ad_response_data['engineMake']['name'] ?? '' );
		$engine_model               = sanitize_text_field( $ad_response_data['engineModel'] ?? '' );
		$engine_model_specification = sanitize_text_field( $ad_response_data['engineModelSpecification'] ?? '' );

		$year = sanitize_text_field( $ad_response_data['year'] ?? '' );

		$title = $maker;

		if ( $model !== '' ) {
			$title .= ' ' . $model;
		}

		if ( $year !== '' ) {
			$title .= ' ' . $year;
		}

		$wc_data['title'] = $title;
		$wc_data['short_description'] = $this->create_short_description_content( $boat_type,
			$boat_sub_type,
			$maker,
			$model,
			$engine_maker,
			$engine_model,
			$engine_model_specification,
			$year,
			$town );

		$wc_data['custom_fields'] = [
			'_wc_integration_to_nettix_boat_type'                  => $boat_type,
			'_wc_integration_to_nettix_boat_sub_type'              => $boat_sub_type,
			'_wc_integration_to_nettix_maker'                      => $maker,
			'_wc_integration_to_nettix_model'                      => $model,
			'_wc_integration_to_nettix_engine_maker'               => $engine_maker,
			'_wc_integration_to_nettix_engine_model'               => $engine_model,
			'_wc_integration_to_nettix_engine_model_specification' => $engine_model_specification,
			'_wc_integration_to_nettix_year'                       => $year,
			'_wc_integration_to_nettix_location'                   => $town,
		];

		$ad_response_data['wc_data'] = $wc_data;
	}

	private function create_short_description_content(
		string $boat_type,
		string $boat_sub_type,
		string $maker,
		string $model,
		string $engine_maker,
		string $engine_model,
		string $engine_model_specification,
		int $year,
		string $location
	): string {
		$ret = "";
		if ( $this->settings->get_service_boat_short_desc_use_list() == 'yes' ) {
			$ret .= "<ul>\n";

			if ( $this->settings->get_service_boat_short_desc_field_boat_type() == 'yes' && $boat_type ) {
				$ret .= "<li>";
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Tyyppi: ";
				}
				$ret .= $boat_type;
				if ( $this->settings->get_service_boat_short_desc_field_boat_sub_type() == 'yes' && $boat_sub_type ) {
					$ret .= " - {$boat_sub_type}";
				}
				$ret .= "</li>\n";
			}

			if ( $this->settings->get_service_boat_short_desc_field_maker() == 'yes' && $maker ) {
				$ret .= "<li>";
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Merkki ja malli: ";
				}
				$ret .= $maker;
				if ( $this->settings->get_service_boat_short_desc_field_model() == 'yes' && $model ) {
					$ret .= " {$model}";
				}
				$ret .= "</li>\n";
			}

			if ( $this->settings->get_service_boat_short_desc_field_engine_maker() == 'yes' && $engine_maker ) {
				$ret .= "<li>";
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Moottori: ";
				}
				$ret .= $engine_maker;
				if ( $this->settings->get_service_boat_short_desc_field_engine_model() == 'yes' && $engine_model ) {
					$ret .= " {$engine_model}";
				}
				if ( $this->settings->get_service_boat_short_desc_field_engine_model_specification() == 'yes' && $engine_model_specification ) {
					$ret .= " {$engine_model_specification}";
				}
				$ret .= "</li>\n";
			}

			if ( $this->settings->get_service_boat_short_desc_field_year() == 'yes' && $year > 0 ) {
				$ret .= "<li>";
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Vuosimalli: ";
				}
				$ret .= "{$year}</li>\n";
			}

			if ( $this->settings->get_service_boat_short_desc_field_location() == 'yes' && $location > 0 ) {
				$ret .= "<li>";
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Sijainti: ";
				}
				$ret .= "{$location}</li>\n";
			}

			$ret .= "</ul>\n";
		} else {
			$added = false;
			if ( $this->settings->get_service_boat_short_desc_field_boat_type() == 'yes' && $boat_type ) {
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Tyyppi: ";
				}
				$ret .= $boat_type;
				if ( $this->settings->get_service_boat_short_desc_field_boat_sub_type() == 'yes' && $boat_sub_type ) {
					$ret .= " - {$boat_sub_type}";
				}
				$added = true;
			}

			if ( $this->settings->get_service_boat_short_desc_field_maker() == 'yes' && $maker ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Merkki ja malli: ";
				}
				$ret .= $maker;
				if ( $this->settings->get_service_boat_short_desc_field_model() == 'yes' && $model ) {
					$ret .= " {$model}";
				}
				$added = true;
			}

			if ( $this->settings->get_service_boat_short_desc_field_engine_maker() == 'yes' && $engine_maker ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Moottori: ";
				}
				$ret .= $engine_maker;
				if ( $this->settings->get_service_boat_short_desc_field_engine_model() == 'yes' && $engine_model ) {
					$ret .= " {$engine_model}";
				}
				if ( $this->settings->get_service_boat_short_desc_field_engine_model_specification() == 'yes' && $engine_model_specification ) {
					$ret .= " {$engine_model_specification}";
				}
				$added = true;
			}

			if ( $this->settings->get_service_boat_short_desc_field_year() == 'yes' && $year > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Vuosimalli: ";
				}
				$ret   .= $year;
				$added = true;
			}

			if ( $this->settings->get_service_boat_short_desc_field_location() == 'yes' && $location > 0 ) {
				if ( $added ) {
					$ret .= ", ";
				}
				if ( $this->settings->get_service_boat_short_desc_use_labels() == 'yes' ) {
					$ret .= "Sijainti: ";
				}
				$ret .= $location;
			}
		}

		return $ret;
	}
}