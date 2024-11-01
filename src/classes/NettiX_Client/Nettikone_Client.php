<?php

namespace WC_Integration_NettiX\NettiX_Client;

use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Nettikone_Client extends Base_Client {
	public function __construct( Plugin_Settings $settings ) {
		parent::__construct( $settings );
		$this->api_url = Base_Client::API_BASE_URL . 'rest/machine/';
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
			'includemakemodel'        => 'includeMakeModel',
			'colortype'               => 'colorType',
			'trailerattachment'       => 'trailerAttachment',
			'fueltype'                => 'fuelType',
			'drivetype'               => 'driveType',
			'cuttingwidth'            => 'cuttingWidth',
			'accessoriescondition'    => 'accessoriesCondition',
			'vatdeduct'               => 'vatDeduct',
			'pricefrom'               => 'priceFrom',
			'priceto'                 => 'priceTo',
			'weekpricefrom'           => 'weekPriceFrom',
			'weekpriceto'             => 'weekPriceTo',
			'daypricefrom'            => 'dayPriceFrom',
			'daypriceto'              => 'dayPriceTo',
			'weightfrom'              => 'weightFrom',
			'weightto'                => 'weightTo',
			'yearfrom'                => 'yearFrom',
			'yearto'                  => 'yearTo',
			'powerfrom'               => 'powerFrom',
			'powerto'                 => 'powerTo',
			'powerunitiskw'           => 'powerUnitIsKw',
			'enginesizefrom'          => 'engineSizeFrom',
			'enginesizeto'            => 'engineSizeTo',
			'kilometersfrom'          => 'kilometersFrom',
			'kilometersto'            => 'kilometersTo',
			'hoursfrom'               => 'hoursFrom',
			'hoursto'                 => 'hoursTo',
			'telescoperangefrom'      => 'telescopeRangeFrom',
			'telescoperangeto'        => 'telescopeRangeTo',
			'lastinspectionmonth'     => 'lastInspectionMonth',
			'lastinspectionyear'      => 'lastInspectionYear',
			'datecreatedfrom'         => 'dateCreatedFrom',
			'datecreatedto'           => 'dateCreatedTo',
			'lastmodifiedfrom'        => 'lastModifiedFrom',
			'lastmodifiedto'          => 'lastModifiedTo',
			'searchtext'              => 'searchText',
			'tagscondition'           => 'tagsCondition',
			'maxliftpowerfrom'        => 'maxLiftPowerFrom',
			'maxliftpowerto'          => 'maxLiftPowerTo',
			'liftheightfrom'          => 'liftHeightFrom',
			'liftheightto'            => 'liftHeightTo',
			'driveheightfrom'         => 'driveHeightFrom',
			'driveheightto'           => 'driveHeightTo',
			'excludecountry'          => 'excludeCountry',
			'excludehidden'           => 'excludeHidden',
			'dateupdatedfrom'         => 'dateUpdatedFrom',
			'dateupdatedto'           => 'dateUpdatedTo',
			'registernumber'          => 'registerNumber',
			'excludeadids'            => 'excludeAdIds',
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
			case 'color':
			case 'colorType':
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