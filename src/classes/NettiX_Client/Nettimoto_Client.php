<?php

namespace WC_Integration_NettiX\NettiX_Client;

use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Nettimoto_Client extends Base_Client {

	public function __construct( Plugin_Settings $settings ) {
		parent::__construct( $settings );
		$this->api_url = Base_Client::API_BASE_URL . 'rest/bike/';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_search_criteria_key_mappings(): array {
		return [
			'sortby'                 => 'sortBy',
			'sortorder'              => 'sortOrder',
			'ismyfavorite'           => 'isMyFavorite',
			'identificationlist'     => 'identificationList',
			'adtype'                 => 'adType',
			'biketype'               => 'bikeType',
			'bodytype'               => 'bodyType',
			'colortype'              => 'colorType',
			'drivetype'              => 'driveType',
			'modeltype'              => 'modelType',
			'includemakemodel'       => 'includeMakeModel',
			'accessoriescondition'   => 'accessoriesCondition',
			'fueltype'               => 'fuelType',
			'geartype'               => 'gearType',
			'coolingtype'            => 'coolingType',
			'powerclass'             => 'powerClass',
			'pricefrom'              => 'priceFrom',
			'priceto'                => 'priceTo',
			'weekpricefrom'          => 'weekPriceFrom',
			'weekpriceto'            => 'weekPriceTo',
			'daypricefrom'           => 'dayPriceFrom',
			'daypriceto'             => 'dayPriceTo',
			'yearfrom'               => 'yearFrom',
			'yearto'                 => 'yearTo',
			'kilometersfrom'         => 'kilometersFrom',
			'kilometersto'           => 'kilometersTo',
			'hoursfrom'              => 'hoursFrom',
			'hoursto'                => 'hoursTo',
			'enginesizefrom'         => 'engineSizeFrom',
			'enginesizeto'           => 'engineSizeTo',
			'trackwidthfrom'         => 'trackWidthFrom',
			'trackwidthto'           => 'trackWidthTo',
			'firstregistrationmonth' => 'firstRegistrationMonth',
			'firstregistrationyear'  => 'firstRegirstrationYear',
			'lastmodifiedfrom'       => 'lastModifiedFrom',
			'lastmodifiedto'         => 'lastModifiedTo',
			'postedby'               => 'postedBy',
			'undrivenvehicle'        => 'undrivenVehicle',
			'coseater'               => 'coSeater',
			'isregistered'           => 'isRegistered',
			'ispriced'               => 'isPriced',
			'taxfree'                => 'taxFree',
			'vatdeduct'              => 'vatDeduct',
			'searchtext'             => 'searchText',
			'createdfrom'            => 'createdFrom',
			'createdto'              => 'createdTo',
			'tagscondition'          => 'tagsCondition',
			'excludehidden'          => 'excludeHidden',
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function sanitize_value( string $key, $value ) {
		switch ( $key ) {
			case 'accessories':
			case 'bikeType':
			case 'bodyType':
			case 'color':
			case 'colorType':
			case 'coolingType':
			case 'driveType':
			case 'fuelType':
			case 'gearType':
			case 'identificationList':
			case 'make':
			case 'model':
			case 'modelType':
			case 'powerClass':
			case 'region':
			case 'status':
			case 'town':
				return explode( ',', sanitize_text_field( $value ) );
			default:
				return sanitize_text_field( $value );
		}
	}
}