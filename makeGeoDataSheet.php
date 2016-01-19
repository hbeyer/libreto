<?php

function makeGeoDataSheet($data, $folderName, $format) {
	$ending = strtolower($format);
	$index1 = makeIndex($data, 'placeName');
	$index2 = makeIndex($data, 'year');
	$commonIndex = mergeIndices($index1, $index2);
	
	$rowArray = array();
	$placeName = '';
	
	var_dump($commonIndex);
	
	foreach($commonIndex as $entry) {
	
		if($entry->level == 1) {
			$placeName = $entry->label;
			$latitude = cleanCoordinate($entry->geoData['lat']);
			$longitude = cleanCoordinate($entry->geoData['long']);
		}
		if($entry->level == 2) {
			$row = new geoDataRow;
			$row->label = $placeName;
			$row->lat = cleanCoordinate($entry->geoData['lat']);
			$row->long = cleanCoordinate($entry->geoData['long']);
			$row->timeStamp = $entry->label;
			$row->lat = $latitude;
			$row->long = $longitude;
			if($entry->authority['system'] == 'getty') {
				$row->getty = $entry->authority['id'];
			}
			elseif($entry->authority['system'] == 'geoNames') {
				$row->geoNames = $entry->authority['id'];
			}
			$rowArray[] = $row;
		}
	}		
		
		if($ending == 'csv') {
			$content = '"Name","Address","Description","Longitude","Latitude","TimeStamp","TimeSpan:begin","TimeSpan:end","GettyID",""
';
			foreach($rowArray as $row) {
				$content .= makePlaceEntryCSV($row);
			}

		}
		elseif($ending == 'kml') {
			$content = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
	<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:xal="urn:oasis:names:tc:ciq:xsdschema:xAL:2.0">
		<Folder>';
				foreach($rowArray as $row) {
					$content .= makePlaceEntryKML($row);
				}	
			$content .= '
		</Folder>
	</kml>';
		}
		
	$fileName = $folderName.'/printingPlaces1.'.$ending;
	$datei = fopen($fileName,"w");
	fwrite($datei, $content, 30000000);
	fclose($datei);
	
}

function makePlaceEntryCSV($rowObject) {
		$row = '"'.$rowObject->label.'","'.$rowObject->label.'","'.$rowObject->label.'","'.$rowObject->long.'","'.$rowObject->lat.'","'.$rowObject->timeStamp.'","","'.$rowObject->getty.'",""
';	
	return($row);
}
	
function makePlaceEntryKML($rowObject) {
	$row = '
		<Placemark>
			<address>'.$rowObject->label.'</address>';
	if($rowObject->timeStamp) {
		$row .=	'
			<TimeStamp>
				<when>'.$rowObject->timeStamp.'</when>
			</TimeStamp>';
	}
	$row .= '
			<Point>
				<coordinates>'.$rowObject->long.','.$rowObject->lat.'</coordinates>
			</Point>
		</Placemark>';
	return($row);
}
	
?>