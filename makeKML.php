<?php

function makeKML($data, $folderName) {
	$placeArray = array();
	foreach($data as $item) {
		$year = normalizeYear($item->year);
		foreach($item->places as $place) {
			$placeName = trim($place->name, '[]');
			if(array_key_exists($placeName, $placeArray) == FALSE) {
				$placeArray[$placeName] = array();
				$placeArray[$placeName]['dates'] = array();
			}
			$placeArray[$placeName]['coordinates'] = $place->geoData;
			$placeArray[$placeName]['dates'][] = $year;
			$year = '';
		}
	}
		
		$content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xal="urn:oasis:names:tc:ciq:xsdschema:xAL:2.0">
	<Folder>';
		$foot = '
	</Folder>
</kml>';
		
		foreach($placeArray as $name => $placemark) {
			$translate = array(',' => '.');
			$translate2 = array('/' => ' ');
			$lat = strtr($placemark['coordinates']['lat'], $translate);
			$long = strtr($placemark['coordinates']['long'], $translate);
			$name = strtr($name, $translate2);
			if($lat != '' and $long != '') {
				foreach($placemark['dates'] as $date) {
					$xml = '
			<Placemark>
				<address>'.$name.'</address>
				<TimeStamp>
					<when>'.$date.'</when>
				</TimeStamp>
				<Point>
					<coordinates>'.$lat.','.$long.'</coordinates>
				</Point>
			</Placemark>';
					$content .= $xml;
				}
			}
		}
		
		$content .= $foot;
		$fileName = $folderName.'/printingPlaces.kml';
		$datei = fopen($fileName,"w");
		fwrite($datei, $content, 3000000);
		fclose($datei);
}
	
?>