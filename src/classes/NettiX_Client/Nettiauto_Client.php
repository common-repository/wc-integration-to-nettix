<?php

namespace WC_Integration_NettiX\NettiX_Client;

use WC_Integration_NettiX\Plugin\Plugin_Settings;

class Nettiauto_Client extends Base_Client {

	public function __construct( Plugin_Settings $settings ) {
		parent::__construct( $settings );
		$this->api_url = Base_Client::API_BASE_URL . 'rest/car/';
	}

	/**
	 * @inheritDoc
	 */
	protected function get_search_criteria_key_mappings(): array {
		return [
			'sortby'                  => 'sortBy',
			'sortorder'               => 'sortOrder',
			'identificationlist'      => 'identificationList',
			'adtype'                  => 'adType',
			'postedby'                => 'postedBy',
			'ismyfavorite'            => 'isMyFavorite',
			'vehicletype'             => 'vehicleType',
			'includemakemodel'        => 'includeMakeModel',
			'bodytype'                => 'bodyType',
			'modeltypename'           => 'modelTypeName',
			'accessoriescondition'    => 'accessoriesCondition',
			'geartype'                => 'gearType',
			'drivetype'               => 'driveType',
			'fueltype'                => 'fuelType',
			'colortype'               => 'colorType',
			'firstregistrationmonth'  => 'firstRegistrationMonth',
			'firstregistrationyear'   => 'firstRegistrationYear',
			'lastinspectionmonth'     => 'lastInspectionMonth',
			'lastinspectionyear'      => 'lastInspectionYear',
			'ispriced'                => 'isPriced',
			'vatdeduct'               => 'vatDeduct',
			'taxfree'                 => 'taxFree',
			'pricefrom'               => 'priceFrom',
			'priceto'                 => 'priceTo',
			'yearfrom'                => 'yearFrom',
			'yearto'                  => 'yearTo',
			'kilometersfrom'          => 'kilometersFrom',
			'kilometersto'            => 'kilometersTo',
			'enginesizefrom'          => 'engineSizeFrom',
			'enginesizeto'            => 'engineSizeTo',
			'powerunitiskw'           => 'powerUnitIsKw',
			'powerfrom'               => 'powerFrom',
			'powerto'                 => 'powerTo',
			'torquefrom'              => 'torqueFrom',
			'torqueto'                => 'torqueTo',
			'towweightwithbrakes'     => 'towWeightWithBrakes',
			'towweightfrom'           => 'towWeightFrom',
			'towweightto'             => 'towWeightTo',
			'accelerationfrom'        => 'accelerationFrom',
			'accelerationto'          => 'accelerationTo',
			'consumptionurbanfrom'    => 'consumptionUrbanFrom',
			'consumptionUrbanto'      => 'consumptionUrbanTo',
			'consumptionroadfrom'     => 'consumptionRoadFrom',
			'consumptionroadt'        => 'consumptionRoadTo',
			'consumptioncombinedfrom' => 'consumptionCombinedFrom',
			'consumptioncombinedto'   => 'consumptionCombinedTo',
			'co2emissionfrom'         => 'co2EmissionFrom',
			'co2emissionto'           => 'co2EmissionTo',
			'seatsfrom'               => 'seatsFrom',
			'seatsto'                 => 'seatsTo',
			'doorsfrom'               => 'doorsFrom',
			'doorsto'                 => 'doorsTo',
			'batterycapacityfrom'     => 'batteryCapacityFrom',
			'batterycapacityto'       => 'batteryCapacityTo',
			'datecreatedfrom'         => 'dateCreatedFrom',
			'datecreatedto'           => 'dateCreatedTo',
			'lastmodifiedfrom'        => 'lastModifiedFrom',
			'lastmodifiedto'          => 'lastModifiedTo',
			'roadworthy'              => 'roadWorthy',
			'steeringwheelleft'       => 'steeringWheelLeft',
			'searchtext'              => 'searchText',
			'electricrangefrom'       => 'electricRangeFrom',
			'electricrangeto'         => 'electricRangeTo',
			'tagscondition'           => 'tagsCondition',
			'curbweightfrom'          => 'curbWeightFrom',
			'curbweightto'            => 'curbWeightTo',
			'excludehidden'           => 'excludeHidden',
			'excludecountry'          => 'excludeCountry',
		];
	}

	/**
	 * @inheritDoc
	 */
	protected function sanitize_value( string $key, $value ) {
		switch ( $key ) {
			case 'accessories':
			case 'bodyType':
			case 'color':
			case 'colorType':
			case 'country':
			case 'driveType':
			case 'excludeCountry':
			case 'fuelType':
			case 'gearType':
			case 'identificationList':
			case 'make':
			case 'model':
			case 'region':
			case 'status':
			case 'tags':
			case 'town':
			case 'vehicleType':
				return explode( ',', sanitize_text_field( $value ) );
			default:
				return sanitize_text_field( $value );
		}
	}
}