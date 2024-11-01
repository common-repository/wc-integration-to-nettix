<?php

namespace WC_Integration_NettiX\NettiX_Client;

use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Nettikaravaani_Client extends Base_Client {

	public function __construct( Plugin_Settings $settings ) {
		parent::__construct( $settings );
		$this->api_url = Base_Client::API_BASE_URL . 'rest/caravan/';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_search_criteria_key_mappings(): array {
		return [
			'sortby'                  => 'sortBy',
			'sortorder'               => 'sortOrder',
			'adtype'                  => 'adType',
			'postedby'                => 'postedBy',
			'identificationlist'      => 'identificationList',
			'ismyfavorite'            => 'isMyFavorite',
			'vehicletype'             => 'vehicleType',
			'includemakemodel'        => 'includeMakeModel',
			'modelinfo'               => 'modelInfo',
			'bodytype'                => 'bodyType',
			'fueltype'                => 'fuelType',
			'drivetype'               => 'driveType',
			'accessoriescondition'    => 'accessoriesCondition',
			'ispriced'                => 'isPriced',
			'vatdeduct'               => 'vatDeduct',
			'taxfree'                 => 'taxFree',
			'pricefrom'               => 'priceFrom',
			'priceto'                 => 'priceTo',
			'weekpricefrom'           => 'weekPriceFrom',
			'weekpriceto'             => 'weekPriceTo',
			'daypricefrom'            => 'dayPriceFrom',
			'daypriceto'              => 'dayPriceTo',
			'yearfrom'                => 'yearFrom',
			'yearto'                  => 'yearTo',
			'kilometersfrom'          => 'kilometersFrom',
			'kilometersto'            => 'kilometersTo',
			'undrivenvehicle'         => 'undrivemVehicle',
			'enginesizefrom'          => 'engineSizeFrom',
			'enginesizeto'            => 'engineSizeTo',
			'powerfrom'               => 'powerFrom',
			'powerto'                 => 'powerTo',
			'powerunitiskw'           => 'powerUnitIsKw',
			'bedsfrom'                => 'bedsFrom',
			'bedsto'                  => 'bedsTo',
			'doorsfrom'               => 'doorsFrom',
			'doorsto'                 => 'doorsTo',
			'seatsfrom'               => 'seatsFrom',
			'seatsto'                 => 'seatsTo',
			'weightfrom'              => 'weightFrom',
			'weightto'                => 'weightTo',
			'deadweightfrom'          => 'deadWeightFrom',
			'deadweightto'            => 'deadWeightTo',
			'lengthfrom'              => 'lengthFrom',
			'lengthto'                => 'lengthTo',
			'widthfrom'               => 'widthFrom',
			'widthto'                 => 'widthTo',
			'speedlimitfrom'          => 'speedLimitFrom',
			'speedlimitto'            => 'speedLimitTo',
			'firstregistrationmonth'  => 'firstRegistrationMonth',
			'firstregistrationyear'   => 'firstRegistrationYear',
			'lastinspectionmonth'     => 'lastInspectionMonth',
			'lastinspectionyear'      => 'lastInspectionYear',
			'notinspected'            => 'notInspected',
			'datecreatedfrom'         => 'dateCreatedFrom',
			'datecreatedto'           => 'dateCreatedTo',
			'lastmodifiedfrom'        => 'lastModifiedFrom',
			'lastmodifiedto'          => 'lastModifiedTo',
			'searchtext'              => 'searchText',
			'tagscondition'           => 'tagsCondition',
			'excludehidden'           => 'excludeHidden',
			'dateupdatedfrom'         => 'dateUpdatedFrom',
			'dateupdatedto'           => 'dateUpdatedTo',
			'registernumber'          => 'registerNumber',
			'excludeadids'            => 'excludeAdIds',
			'excludecountry'          => 'excludeCountry',
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function sanitize_value( string $key, $value ) {
		switch ( $key ) {
			case 'accessories':
			case 'adType':
			case 'bodyType':
			case 'categories':
			case 'country':
			case 'cuttingWidth':
			case 'driveType':
			case 'excludeAdIds':
			case 'excludeCountry':
			case 'fuelType':
			case 'gearType':
			case 'identificationList':
			case 'make':
			case 'model':
			case 'region':
			case 'registerNumber':
			case 'status':
			case 'tags':
			case 'town':
			case 'trailerAttachment':
			case 'uuid':
			case 'vehicleType':
				return explode( ',', sanitize_text_field( $value ) );
			default:
				return sanitize_text_field( $value );
		}
	}
}