<?php

$path = 'upload/test.csv';
$data = loadCSV($path);
$serialize = serialize($data);
file_put_contents('upload/test', $serialize);

function loadCSV($path) {
	$data = array();
	$csv = file_get_contents($path);
	$document = str_getcsv($csv, "\n");
	$firstRow = TRUE;
	foreach($document as $row) {
		$fields = str_getcsv($row, ";");
		$fields = array_map('convertWindowsToUTF8', $fields);
		if($firstRow == TRUE) {
			$fieldNames = $fields;
			$firstRow = FALSE;
		}
		elseif($firstRow == FALSE) {
			$item = makeItemFromCSVRow($fields);
			$data[] = $item;
		}
	}
	return($data);
}

function makeItemFromCSVRow($row) {
	$item = new item();
	$item->id = $row[0];
	$item->pageCat = $row[1];
	$item->imageCat = $row[2];
	$item->numberCat = $row[3];
	$item->itemInVolume = $row[4];
	$item->titleCat = $row[5];
	$item->titleBib = $row[6];
	$item->titleNormalized = $row[7];
	$item->publisher = $row[18];
	$item->year = $row[19];
	$item->format = $row[20];
	$item->histSubject = $row[21];
	$item->subject = $row[22];
	$item->genre = $row[23];
	$item->mediaType = $row[24];
	$item->bibliographicalLevel = $row[28];		
	$item->manifestation = array('systemManifestation' => $row[29], 'idManifestation' => $row[30]);
	$item->originalItem =  array('institutionOriginal' => $row[31], 'shelfmarkOriginal' => $row[32], 'provenanceAttribute' => $row[33], 'digitalCopyOriginal' => $row[34], 'targetOPAC' => $row[35], 'searchID' => $row[36]);
	$item->work = array('titleWork' => $row[37], 'systemWork' => $row[38], 'idWork' => $row[39]);		
	$item->bound = $row[40];
	$item->comment = $row[41];
	$item->digitalCopy = $row[42];
	
	$languageFields = array($row[25], $row[26], $row[27]);
	foreach($languageFields as $languageCode) {
		if($languageCode) {
			$item->language[] = $languageCode;
		}
	}
	
	$authorFields = array($row[8], $row[9], $row[10], $row[11]);
	foreach($authorFields as $authorString) {
		if($authorString != '') {
			$item->persons[] = makePersonFromCSV($authorString, 'author');
		}
	}
	
	$contributorFields = array($row[12], $row[13], $row[14], $row[15]);
	foreach($contributorFields as $contributorString) {
		if($contributorString != '') {
			$item->persons[] = makePersonFromCSV($contributorString, 'contributor');
		}
	}

	$placeFields = array($row[16], $row[17]);
	foreach($placeFields as $placeString) {
		if($placeString != '') {
			$parts = explode('#', $placeString);
			$place = new place();
			$place->placeName = $parts[0];
			if(isset($parts[1])) {
				if(substr($parts[1], 0, 8) == 'geoNames') {
					$place->geoNames = substr($parts[1], 8);
				}
				elseif(substr($parts[1], 0, 5) == 'getty') {
					$place->getty = substr($parts[1], 5);
				}	
			}
			$item->places[] = $place;
		}
	}
	
	return($item);
}

function makePersonFromCSV($string, $role) {
	$parts = explode('#', $string);
	$person = new person();
	$person->role = $role;
	$person->persName = $parts[0];
	if(isset($parts[1])) {
		$person->gnd = $parts[1];
	}
	return($person);
}

?>