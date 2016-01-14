<?php

function preprocessValues($value, $field) {
	if($field == 'person') {
		$value = removeSpecial(trim($value, '[]'));
	}
	elseif($field == 'place') {
		$value = trim($value, '[]');
	}
	elseif($field == 'year') {
		$value = normalizeYear($value);
	}
	return($value);
}

function treatVoid($data, $field, $id) {
	if($field == 'year') {
		$value = getYearFromTitle($data[$id]->titleCat);
		if($value == '') {
			$value = 'ohne Jahr';
		}
	}
	if($value == '') {
		$value = 'ohne Kategorie';
	}
	return($value);
}

function collectIDs($data, $field) {
	$collect = array();
	$index = array();
	$count = 0;
	foreach($data as $item) {
		if(array_key_exists($item->$field, $collect) == FALSE) {
			$collect[$item->$field] = array();
		}
		$collect[$item->$field][] = $count;
		$count ++;
	}
	return($collect);
}

function collectIDsArrayValues($data, $field) {
	$collect = array();
	$index = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->$field as $value) {
			if(array_key_exists($value, $collect) == FALSE) {
				$collect[$value] = array();
			}
			$collect[$value][] = $count;
		}
		$count ++;
	}
	return($collect);
}

function collectIDsSubObjects($data, $field, $subField) {
	$collect = array();
	$index = array();
	$count = 0;
	foreach($data as $item) {
		foreach($item->field as $subItem) {
			if(array_key_exists($subItem->$subField, $collect) == FALSE) {
				$collect[$subItem->$subField] = array();
			}
			$collect[$subItem->$subField][] = $count;
		}
	$count ++;
	}
	return($collect);
}

function makeEntries($data, $collect) {
	$index = array();
	foreach($collect as $value => $IDs) {
		$entry = new indexEntry();
		$entry->label = $value;
		foreach($IDs as $id) {
			$entry->content[] = $data[$id];
		}
		// Hier müsste man noch GND-Nummern und Geodaten hinzufügen (etwa aus dem ersten Objekt), sofern sich das nicht intelligenter lösen lässt.
	}
	return($index);
}

function mergeIndices($index1, $index2) {
	$commonIndex = array();
	foreach($index1 as $index1) {
		$specialContent = $index1->content;
		$buffer = array();
		foreach($index2 as $index2) {
			$intersection = array_intersect($index1->content, $index2->content);
			$specialContent = array_diff($specialContent, $index2->content);
			$entry2 = new indexEntry();
			$entry2->level = 2;
			$entry2->label = $index2->label;
			$entry2->content = $intersection;
			$buffer[] = $entry2;
			}
		$entry1 = new indexEntry();
		$entry1->label = $index1->label;
		$commonIndex[] = $entry1;
		foreach($buffer as $entryDown) {
			$commonIndex[] = $entryDown; 
		}
		if($specialContent) {
			$entryRemains = new indexEntry();
			$entryRemains->level = 2;
			$entryRemains->label = 'ohne Kategorie';
			$entryRemains->content = $specialContent;
			$commonIndex[] = $entryRemains;
		}
		
		/* Für jeden Eintrag des ersten Index {
				Für jeden Eintrag des zweiten Index die Schnittmenge mit den Identifiern des ersten Index bestimmen (array_intersect).
				Die Schnittmenge in einer Variable speichern, den Sonderbestand des ersten Index in einer anderen. 
				In einer dritten Variable die Schnittmenge zwischen dem aktuellen und dem vorherigen Sonderbestand speichern.
			}
			Für jeden Eintrag des ersten Index ein Objekt anlegen, das den verbleibenden Sonderbestand als Content enthält.
			Für jede dazu gefundene Schnittmenge ein Unterobjekt anlegen, das diese enthält.
		*/
	}
	return($commonIndex);
}

/* 
Zu lösendes Problem: Das collect-Array für Autoren muss nach der GND-Nummer, soweit vorhanden, gebildet werden, als Label für den Index müssen aber die Namen stehen. Funktion collectIDsByOtherField()?
*/


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
				//$name = $name;
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
		$gettyArray = array();
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
						$gettyArray[$key] = $place->getty;
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
			if(isset($gettyArray[$place])) {
				$entry->authority['system'] = 'getty';
				$entry->authority['id'] = $gettyArray[$place];
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

?>
