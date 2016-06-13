<?php

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
	$item->subjects = explode(';', $row[22]);
	$item->genres = explode(';', $row[23]);
	$item->mediaType = $row[24];
	$item->manifestation = array('systemManifestation' => $row[26], 'idManifestation' => $row[27]);
	$item->originalItem =  array('institutionOriginal' => $row[28], 'shelfmarkOriginal' => $row[29], 'provenanceAttribute' => $row[30], 'digitalCopyOriginal' => $row[31], 'targetOPAC' => $row[32], 'searchID' => $row[33]);
	$item->work = array('titleWork' => $row[34], 'systemWork' => $row[35], 'idWork' => $row[36]);		
	$item->bound = $row[37];
	$item->comment = $row[38];
	$item->digitalCopy = $row[39];
	$item->languages = explode(';', $row[25]);
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
					$geoNames = substr($parts[1], 8);
					$place->geoNames = testGeoNames($geoNames);
				}
				elseif(substr($parts[1], 0, 3) == 'gnd') {
					$gnd = substr($parts[1], 3);
					$place->gnd = testGND($gnd);
				}				
				elseif(substr($parts[1], 0, 5) == 'getty') {
					$getty = substr($parts[1], 5);
					$place->getty = testGetty($getty);
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
		if(preg_match('~^[0-9X]+[mfx]$~', $parts[1]) == 1) {
			$person->gender = substr($parts[1], -1, 1);
			$person->gnd = substr($parts[1], 0, -1);
		}
		else {
			$person->gnd = $parts[1];
		}
	}
	return($person);
}

function testGeoNames($id) {
	$return = '';
	if(preg_match('~^[0-9]{5,9}$~', $id) == 1) {
		return($id);
	}
}

function testGND($gnd) {
	$return = '';
	if(preg_match('~^[0-9X-]{9,11}$~', $gnd) == 1) {
		return($gnd);
	}	
}

function testGetty($id) {
	if(preg_match('~^[0-9]{5,9}$~', $id) == 1) {
		return($id);
	}
}

?>
