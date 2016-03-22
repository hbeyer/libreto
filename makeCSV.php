<?php

function makeRowCSV($item) {
	$row = array();
	foreach($item as $key => $value) {
		if(is_array($value)) {
			if($key == 'language') {
				$count = 0;
				while($count < 3) {
					$row[] = $value[$count];
					$count++;
				}
			}
			elseif($key == 'persons') {
				$row = array_merge($row, makePersonRowCSV($value));
			}
			elseif($key == 'places') {
				$row = array_merge($row, makePlaceRowCSV($value));
			}
			else {
				foreach($value as $subfieldContent) {
					$row[] = $subfieldContent;
				}
			}
		}
		else {
			$row[] = $value;
		}
	}
	return($row);
}

function makePersonRowCSV($persList) {
	$authors = array();
	$contributors = array();
	$count = 0;
	foreach($persList as $person) {
		if($person->role == 'author' or $person->role == 'creator') {
			$authors[] = $person;
		}
		else {
			$contributors[] = $person;
		}
	}
	$row = insertFourPersons($authors);
	$row = array_merge($row, insertFourPersons($contributors));
	return($row);
}

function insertFourPersons($persons) {
	$subRow = array();
	$count = 0;
	foreach($persons as $person) {
		while($count < 4) {
			$gndString = '';
			if($person->gnd) {
				$gndString = '#'.$person->gnd;
			}
			$subRow[] = $person->persName.$gndString;
			$count++;
		}
	}
	while($count < 4) {
		$subRow[] = '';
		$count++;
	}
	return($subRow);
}

function makePlaceRowCSV($placeList) {
	$row = array();
	$count = 0;
	foreach($placeList as $place) {
		while($count < 2) {
			$placeAuthority = '';
			if($place->geoNames) {
				$placeAuthority = '#geoNames_'.$place->geoNames;
			}
			elseif($place->getty) {
				$placeAuthority = '#getty_'.$place->geoNames;
			}
			$row[] = $placeName.$placeAuthority;
			$count++;
		}
	}
	while($count < 2) {
		$row[] = '';
		$count++;
	}
	return($row);
}

?>