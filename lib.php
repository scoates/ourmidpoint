<?php
require __DIR__ . '/config.php';

function fetch($leftCity, $rightCity, $dep) {

	// http://api.ean.com/ean-services/rs/air/200919/xmlinterface.jsp
	// cid=55505&resType=air&intfc=ws
	// &apiKey=[xxxYourAPIkeyxxx]
	// &xml=

	$xml = '<AirSessionRequest method="getAirAvailability">
	<AirAvailabilityQuery>
	<originCityCode>' . $leftCity . '</originCityCode>
	<destinationCityCode>' . $rightCity . '</destinationCityCode>
	<departureDateTime>' . date('m/d/Y', $dep) . ' 9:00 AM</departureDateTime>
	<tripType>O</tripType>
	<Passengers>
		<adultPassengers>1</adultPassengers>
	</Passengers>
	<xmlResultFormat>2</xmlResultFormat>
	<searchType>2</searchType>
	</AirAvailabilityQuery>
	</AirSessionRequest>';

	$result = file_get_contents('http://api.ean.com/ean-services/rs/air/200919/xmlinterface.jsp?' . http_build_query(array(
		'cid' => '000',
		'resType' => 'air',
		'intfc' => 'ws',
		'apiKey' => Config::API_KEY,
		'xml' => $xml,
	)));
	return $result;
}

function parse($x, $leftCity, $rightCity) {

	$left = $right = array(
		'city' => '',
		'directAirports' => array(),
	);

	$left['city'] = $leftCity;
	$right['city'] = $rightCity;

	foreach ($x->SegmentList->Segment as $seg) {
		if ($seg->originCityCode == $left['city']) {
			$left['directAirports'][] = $seg->destinationCityCode;
		} elseif ($seg->destinationCityCode == $right['city']) {
			$right['directAirports'][] = $seg->originCityCode;
		}
	}
	$left['directAirports'] = array_unique($left['directAirports']);
	$right['directAirports'] = array_unique($right['directAirports']);
	return compact('left','right');
}
