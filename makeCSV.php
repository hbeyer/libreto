<?php

function makeCSV($data, $fileName) {
	$columns = array(
		'id',
		'pageCat',
		'imageCat',
		'numberCat',
		'itemInVolume',
		'titleCat',
		'titleBib',
		'titleNormalized',
		'author1',
		'author2',
		'author3',
		'author4',
		'contributor1',
		'contributor2',
		'contributor3',
		'contributor4',
		'place1',
		'place2',
		'publisher',
		'year',
		'format',
		'histSubject',
		'subject',
		'genre',
		'mediaType',
		'language1',
		'language2',
		'language3',
		'bibliographicalLevel',
		'systemManifestation',
		'idManifestation',		
		'institutionOriginal',
		'shelfmarkOriginal',
		'provenanceAttribute',
		'digitalCopyOriginal',		
		'targetOPAC',		
		'searchID',		
		'titleWork',
		'systemWork',
		'idWork',		
		'bound',
		'comment',
		'digitalCopy'
		);
		
	$handle = fopen($fileName.'/'.$fileName.'.csv', "w");
	fwrite($handle, "sep=,\n", 100);
	fputcsv($handle, $columns);
	
	foreach($data as $item) {
		$row = makeRowCSV($item);
		$row = array_map('convertToWindowsCharset', $row);
		fputcsv($handle, $row);
	}	
}

function makeRowCSV($item) {
	$row = array();
	foreach($item as $key => $value) {
		if(is_array($value)) {
			if($key == 'language') {
				$count = 0;
				foreach($value as $language) {
					if($count < 3) {
						$row[] = $language;
						$count++;
					}
				}
				while($count < 3) {
					$row[] = '';
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
		if($count < 4) {	
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
		if($count < 2) {
			$placeAuthority = '';
			if($place->geoNames) {
				$placeAuthority = '#geoNames'.$place->geoNames;
			}
			elseif($place->getty) {
				$placeAuthority = '#getty'.$place->getty;
			}
			$row[] = $place->placeName.$placeAuthority;
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