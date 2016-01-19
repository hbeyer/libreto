<?php

function mergeIndices($index1, $index2) {
	$commonIndex = array();
	foreach($index1 as $entry1) {
		$higherEntry = new indexEntry();
		$higherEntry->label = $entry1->label;
		$higherEntry->authority = $entry1->authority;
		$higherEntry->geoData = $entry1->GeoData;
		$commonIndex[] = $higherEntry;
		foreach($index2 as $entry2) {
			$intersection = array_intersect($entry1->content, $entry2->content);
			if($intersection) {
				$lowerEntry = new indexEntry();
				$lowerEntry->level = 2;
				$lowerEntry->label = $entry2->label;
				$lowerEntry->authority = $entry2->authority;
				$lowerEntry->geoData = $entry2->GeoData;
				$lowerEntry->content = $intersection;
				$commonIndex[] = $lowerEntry;
			}
		}
	}
	return($commonIndex);
}

function makeIndex($data, $field) {
	$index = '';
	$normalFields = array('id', 'pageCat', 'imageCat', 'numberCat', 'itemInVolume', 'bibliographicalLevel', 'titleCat', 'titleBib', 'titleNormalized', 'publisher', 'year', 'format', 'histSubject', 'subject', 'genre', 'mediaType', 'bound', 'comment', 'digitalCopy');
	$personFields = array('gnd', 'role');
	$placeFields = array('getty', 'geoNames');
	$arrayFields = array('language');
	
	if(in_array($field, $normalFields)) {
		$collect = collectIDs($data, $field);
	}
	elseif($field == 'persName') {
		$collect = collectIDsPersons($data);
	}
	elseif($field == 'placeName') {
		$collect = collectIDsPlaces($data);
	}
	elseif(in_array($field, $personFields)) {
		$collect = collectIDsSubObjects($data, 'persons', $subField);
	}
	elseif(in_array($field, $placeFields)) {
		$collect = collectIDsSubObjects($data, 'places', $subField);
	}
	elseif(in_array($field, $arrayFields)) {
		$collect = collectIDsArrayValues($data, $field);
	}
	
	if(isset($collect)) {
		$collect = sortCollect($collect);
		$index = makeEntries($collect);
	}
	elseif($field == 'cat') {
		$collect1 = collectIDs($data, 'histSubject');
		$index1 = makeEntries($collect1);
		unset($collect1);
		$collect2 = collectIDs($data, 'format');
		$index2 = makeEntries($collect2);
		unset($collect2);
		$index = mergeIndices($index1, $index2);
	}
	
	foreach($index as $entry) {
		$entry->label = postprocessFields($field, $entry->label);
	}
	
	return($index);
}
function makeEntries($collect) {
	$collectLoop = $collect['collect'];
	$index = array();
	foreach($collectLoop as $value => $IDs) {
		$entry = new indexEntry();
		// Prüfen, ob Personennamen in einem eigenen Array hinterlegt wurden (Funktion collectIDsPersons)
		if(isset($collect['concordanceGND'])) {
			$entry->label = $collect['concordanceGND'][$value];
			if(is_numeric($value)) {
				$entry->authority['system'] = 'gnd';
				$entry->authority['id'] = strval($value);
			}
		}
		else {
			$entry->label = $value;
		}
		// Prüfen, ob Geodaten in einem eigenen Array hinterlegt wurden (Funktion collectIDsPlaces)
		if(isset($collect['concordanceGeoData'])) {
			$entry->geoData = $collect['concordanceGeoData'][$value];
		}
		$entry->content = $IDs;
		$index[] = $entry;
	}
	return($index);
}

function collectIDs($data, $field) {
	$collect = array();
	$count = 0;
	foreach($data as $item) {
		$key = preprocessFields($field, $item->$field, $item);
		if(array_key_exists($key, $collect) == FALSE) {
			$collect[$key] = array();
		}
		$collect[$key][] = $count;
		$count ++;
	}
	$return = array('collect' => $collect);
	return($return);
}

function collectIDsArrayValues($data, $field) {
	$collect = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->$field as $key) {
			$key = preprocessFields($field, $key, $item);
			if(array_key_exists($key, $collect) == FALSE) {
				$collect[$key] = array();
			}
			$collect[$key][] = $count;
		}
		$count ++;
	}
	$return = array('collect' => $collect);
	return($return);
}

function collectIDsSubObjects($data, $field, $subField) {
	$collect = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->$field as $subItem) {
			$key = preprocessFields($subField, $subItem->$subField, $item);
			if(array_key_exists($key, $collect) == FALSE) {
				$collect[$key] = array();
			}
			$collect[$key][] = $count;
		}
	$count ++;
	}
	$return = array('collect' => $collect);
	return($return);
}

function collectIDsPersons($data) {
	$collectGND = array();
	$collectName = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->persons as $person) {
		$key = $person->gnd;
		$name = preprocessFields('persName', $person->persName, $item);
		if($key == '') {
				$key = $name;
		}
		if(array_key_exists($key, $collectGND) == FALSE) {
			$collectGND[$key] = array();
			$collectName[$key] = $name;
		}
		$collectGND[$key][] = $count;
	}
	$count++;
	}
	$return = array('collect' => $collectGND, 'concordanceGND' => $collectName);
	return($return);
}

function collectIDsPlaces($data) {
	$collectPlaceName = array();
	$collectGeoData = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->places as $place) {
			$key = preprocessFields('placeName', $place->placeName, $item);
			if(array_key_exists($key, $collectPlaceName) == FALSE) {
				$collectPlaceName[$key] = array();
				$collectGeoData[$key] = $place->geoData;
			}
			$collectPlaceName[$key][] = $count;
		}
	$count++;
	}
	$return = array('collect' => $collectPlaceName, 'concordanceGeoData' => $collectGeoData);
	return($return);
}

function preprocessFields($field, $value, $item) {
	if($field == 'persName') {
		$value = removeSpecial(trim($value, '[]'));
		$value = replaceArrowBrackets($value);
	}
	elseif($field == 'placeName') {
		$value = trim($value, '[]');
		$test = preg_match('~[oOsS][\. ]?[OlL]|[oO]hne Ort|[sS]ine [lL]oco|[oO]hne Druckort|[oO]hne Angabe~', $value);
		if($value == '' or $test == 1) {
			$value = 's. l.';
		}
	}
	elseif($field == 'year') {
		$value = normalizeYear($value);
		if($value == '') {
			$value = getYearFromTitle($item->titleCat);
		}
		if($value == '') {
			$value = 9999; // Makes empty year fields be sorted to the end
		}
	}
	elseif($field == 'format') {
		$value = sortingFormat($value);
	}
	elseif($value == '') {
		$value = 'ohne Kategorie';
	}
	return($value);
}

function postprocessFields($field, $value) {
	/* Ist nicht ideal, weil auch label vom Typ histSubject erfasst werden, aber vermutl. 
	keine praktische Auswirkung, weil die Ersetzungsfunktion sehr eng gefasst ist. */
	if($field == 'cat') {
		$value = reverseSortingFormat($value);
	}
	if($field == 'year') {
		if($value == 9999) {
			$value = 'ohne Jahr';
		}
	}
	return($value);
}

function sortCollect($collect) {
	if(isset($collect['concordanceGND'])) {
		$sortingConcordance = array_flip($collect['concordanceGND']);
		ksort($sortingConcordance);
		$new = array();
		foreach($sortingConcordance as $name => $gnd) {
			$new[$gnd] = $collect['collect'][$gnd];
		}
		$collect['collect'] = $new;
	}
	else {
		ksort($collect['collect']);
	}
	return($collect);
}

function normalizeYear($year) {
	if(preg_match('~([12][0-9][0-9][0-9])[-– ]{1,3}([12][0-9][0-9][0-9])~', $year, $treffer)) {
		$yearAssign = intval(($treffer[1] + $treffer[2]) / 2);
	}
	elseif(preg_match('~[12][0-9][0-9][0-9]~', $year, $treffer)) {
		$yearAssign = $treffer[0];
	}
	else {
		$yearAssign = '';
	}
	return($yearAssign);
}
	
function getYearFromTitle($title) {
	$yearAssign = '';
	if(preg_match('~ ([12][0-9][0-9][0-9])$| ([12][0-9][0-9][0-9])[^0-9]~', $title, $treffer)) {
		if(isset($treffer[2])) {
			$yearAssign = $treffer[2];
		}
		elseif(isset($treffer[1])) {
			$yearAssign = $treffer[1];
		}
	}
	return($yearAssign);
}

?>