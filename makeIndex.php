<?php

/* include('classDefinition.php');
//include('ingest.php');
include('makeSection.php');
include('sort.php');
include('encode.php');
 */
function removeSpecial($name) {
	$translation = array('Á' => 'A', 'Ł' => 'L', 'Ǧ' => 'G');
	$name = strtr($name, $translation);
	return($name);	
}

function makeIndex($data, $field) {
	$collect = array();
	$index = array();
	if($field == 'persons') {
		$nameArray = array();
		$count = 0;
		foreach($data as $item) {
			foreach($item->persons as $person) {
				$key = $person->gnd;
				$name = removeSpecial(trim($person->name, '[]'));
				$name = $name;
				if($key == '') {
					$key = $name;
				}
				if(array_key_exists($key, $collect) == FALSE) {
					$collect[$key] = array();
					$nameArray[$name] = $key;					
				}
				$collect[$key][] = $count;
			}			
				$count++;
		}
		uksort($nameArray, 'strnatcasecmp');
		foreach($nameArray as $name => $gnd) {
			$entry = new indexEntry();
			$entry->label = $name;
			$entry->type = 'author';
			if(preg_match('~[0-9]+~', $gnd) == 1) {
				$entry->authority['system'] = 'gnd';
				$entry->authority['id'] = $gnd;
			}
			$entry->content = $collect[$gnd];
			$index[] = $entry;
		}
	}
	elseif($field == 'places') {
		$geoArray = array();
		$count = 0;
		foreach($data as $item) {
			foreach($item->places as $place) {
				$key = trim($place->name, '[]');
				if($key == '' or preg_match('~[sSo][\.]?[ ]?[lLO][\.]?~', $key)) {
					$key = 's.l.';
				}
				if(array_key_exists($key, $collect) == FALSE) {
					$collect[$key] = array();
					if($place->geoData) {					
						$geoArray[$key] = $place->geoData;
					}					
				}
				$collect[$key][] = $count;
			}
			$count++;
		}
		ksort($collect);
		foreach($collect as $place => $content) {
			$entry = new indexEntry();
			$entry->label = $place;
			$entry->content = $content;
			if(isset($geoArray[$place])) {
				$entry->geoData = array('lat' => $geoArray[$place]['lat'], 'long' => $geoArray[$place]['long']);
			}
			$index[] = $entry;
		}
	}
	elseif($field == 'language') {
		include('languageCodes.php');
		$count = 0;
		foreach($data as $item) {
			foreach($item->language as $language) {
				$key = $language;	
				if($key == '') {
					$key = 'ohne Sprachangabe';
				}
				if(array_key_exists($key, $collect) == FALSE) {
					$collect[$key] = array();
				}
				$collect[$key][] = $count;
			}
			$count++;	
		}
		foreach($collect as $language => $occurrences)	{
			$entry = new indexEntry();
			$entry->label = $languageCodes[$language];
			if($entry->label == '') {
				$entry->label = $language;
			}
			$entry->authority['system'] = 'ISO 639.2';
			$entry->authority['id'] = $language;
			$entry->content = $occurrences;
			$index[] = $entry;
		}
		usort($index, 'languageIndex');
	} 
	else {
		$count = 0;
		foreach($data as $item) {
			if($field == 'manifestation') {
				$key = $item->manifestation['system'];
			}
			else {
				$key = trim($item->$field, '[]');
			}
			if($field == 'year') {
				$key = normalizeYear($item->$field);
				if($key == '') {
					$key = getYearFromTitle($item->titleCat);
				}
			}
			if($field == 'format') {
				$key = sortingFormat($key);
			}
			if($field == 'genre') {
				$key = insertSpace($key);
			}
			if($key == '') {
				$key = 'leer';
			}
			if(array_key_exists($key, $collect) == FALSE) {
				$collect[$key] = array();
			}
				$collect[$key][] = $count;
			$count++;
		}
		ksort($collect);
		foreach($collect as $key => $content) {
			$entry = new indexEntry();
			if($field == 'format') {
				$entry->label = reverseSortingFormat($key);
			}
			else {
				$entry->label = $key;
			}
			$entry->content = $content;
			$index[] = $entry;
		}
	}

	return($index);
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

/* $dataString = file_get_contents('data-rehl');
$data = unserialize($dataString);
unset($dataString);
	
$test = makeIndex($data, 'persons');
var_dump($test); */

/* foreach($data as $item) {
	echo '<p>'.implode('/', $item->language).': '.$item->titleBib.'</p>';
} */

/*  foreach($test as $entry) {
	echo '<p><b>'.$entry->label.'</b>: ';
	foreach($entry->content as $id) {
		var_dump($data[$id]->places).'<br />';
	}
	echo '</p>';
} */

?>
