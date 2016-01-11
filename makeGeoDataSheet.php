<?php

/* include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('sort.php');
include('encode.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php'); */


function makeGeoDataSheet($data, $folderName, $format) {
	$ending = strtolower($format);
	$index = makeIndex($data, 'places');
	$rowArray = array();

		foreach($index as $entry) {
			$name = '';
			$lat = '';
			$long = '';
			$year = '';
			$getty = '';
			
			$row = new geoDataRow;
			$row->label = replaceSlash($entry->label);
			$row->lat = cleanCoordinate($entry->geoData['lat']);
			$row->long = cleanCoordinate($entry->geoData['long']);
			foreach($entry->content as $itemID) {
				$row->timeStamp = normalizeYear($data[$itemID]->year);
				if($row->timeStamp == '') {
					$row->timeStamp = getYearFromTitle($data[$itemID]->titleCat);
					}
				if(strtolower($entry->authority['system']) == 'getty') {
					$row->getty = $entry->authority['id'];
				}
			$rowArray[] = $row;
			}
		}
		
		unset($row);
		
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
		
	$fileName = $folderName.'/printingPlaces.'.$ending;
	$datei = fopen($fileName,"w");
	fwrite($datei, $content, 3000000);
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
				<address>'.$rowObject->label.'</address>
				<TimeStamp>
					<when>'.$rowObject->timeStamp.'</when>
				</TimeStamp>
				<Point>
					<coordinates>'.$rowObject->long.','.$rowObject->lat.'</coordinates>
				</Point>
			</Placemark>';	
	return($row);
}

/* $dataString = file_get_contents('BibliothekBenediktBahnsens/data-bahn');
$data = unserialize($dataString);
unset($dataString);

makeGeoDataSheet($data, 'BibliothekBenediktBahnsens', 'KML');
makeGeoDataSheet($data, 'BibliothekBenediktBahnsens', 'CSV'); */
	
?>