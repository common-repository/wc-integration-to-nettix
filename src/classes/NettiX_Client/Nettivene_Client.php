<?php

namespace WC_Integration_NettiX\NettiX_Client;

use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Nettivene_Client extends Base_Client {

	public function __construct( Plugin_Settings $settings = null ) {
		parent::__construct( $settings );
		$this->api_url = Base_Client::API_BASE_URL . 'rest/boat/';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_search_criteria_key_mappings(): array {
		return [
			'sortby'               => 'sortBy',
			'sortorder'            => 'sortOrder',
			'identificationlist'   => 'identificationList',
			'adtype'               => 'adType',
			'postedby'             => 'postedBy',
			'ismyfavorite'         => 'isMyFavorite',
			'boattype'             => 'boatType',
			'subtype'              => 'subType',
			'includemakemodel'     => 'includeMakeModel',
			'pricefrom'            => 'priceFrom',
			'priceto'              => 'priceTo',
			'weekpricefrom'        => 'weekPriceFrom',
			'weekpriceto'          => 'weekPriceTo',
			'daypricefrom'         => 'dayPriceFrom',
			'daypriceto'           => 'dayPriceTo',
			'boatlengthfrom'       => 'boatLengthFrom',
			'boatlengthto'         => 'boatLengthTo',
			'boatwidthfrom'        => 'boatWidthFrom',
			'boatwidthto'          => 'boatWidthTo',
			'boatweightfrom'       => 'boatWeightFrom',
			'boatweightto'         => 'boatWeightTo',
			'boatheightfrom'       => 'boatHeightFrom',
			'boatheightto'         => 'boatHeightTo',
			'noofbedsfrom'         => 'noOfBedsFrom',
			'noOfBedsTo'           => 'noOfBedsTo',
			'yearfrom'             => 'yearFrom',
			'yearto'               => 'yearTo',
			'boatdraftfrom'        => 'boatDraftFrom',
			'boatdraftto'          => 'boatDraftTo',
			'sailsteering'         => 'sailSteering',
			'bodymaterial'         => 'bodyMaterial',
			'undrivenboat'         => 'undrivenBoat',
			'boatwithoutengine'    => 'boatWithoutEngine',
			'enginemake'           => 'engineMake',
			'enginepowerfrom'      => 'enginePowerFrom',
			'enginepowerto'        => 'enginePowerTo',
			'enginerig'            => 'engineRig',
			'enginemfgyearfrom'    => 'engineMfgYearFrom',
			'enginemfgyearto'      => 'engineMfgYearTo',
			'enginetype'           => 'engineType',
			'enginestroke'         => 'engineStroke',
			'enginefueltype'       => 'engineFuelType',
			'coolingtype'          => 'coolingType',
			'heatingmake'          => 'heatingMake',
			'heatingfueltype'      => 'heatingFuelType',
			'sailcondition'        => 'sailCondition',
			'sailtype'             => 'sailType',
			'sailmake'             => 'sailMake',
			'sailmaterial'         => 'sailMaterial',
			'accessoriescondition' => 'accessoriesCondition',
			'searchtext'           => 'searchText',
			'lastmodifiedfrom'     => 'lastModifiedFrom',
			'lastmodifiedto'       => 'lastModifiedTo',
			'datecreatedfrom'      => 'dateCreatedFrom',
			'datecreatedto'        => 'dateCreatedTo',
			'tagscondition'        => 'tagsCondition',
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function sanitize_value( string $key, $value ) {
		switch ( $key ) {
			case 'accessories':
			case 'bodyMaterial':
			case 'coolingType':
			case 'engineMake':
			case 'engineType':
			case 'engineStroke':
			case 'engineFuelType':
			case 'heatingMake':
			case 'heatingFuelType':
			case 'identificationList':
			case 'make':
			case 'region':
			case 'sailMake':
			case 'sailMaterial':
			case 'sailType':
			case 'subType':
			case 'status':
			case 'tags':
			case 'town':
				return explode( ',', sanitize_text_field( $value ) );
			default:
				return sanitize_text_field( $value );
		}
	}
}