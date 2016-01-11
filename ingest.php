<?php

function load_data_mysql($server, $user, $password, $database, $table) {

	$db = new mysqli($server, $user, $password, $database);
	$db->set_charset("utf8");
	if (mysqli_connect_errno()) {
		die ('Konnte keine Verbindung zur Datenbank aufbauen: '.mysqli_connect_error().'('.mysqli_connect_errno().')');
	}
	if($result = $db->query('SELECT * FROM '.$table)) {
			$dataArray = array();
			while($rowBooks = $result->fetch_assoc()) {
				$rowBooks = array_map('trim', $rowBooks);
				
				$thisBook = new item();
				
				$thisBook->id = $rowBooks['id'];
				$thisBook->imageCat = $rowBooks['image'];
				$thisBook->pageCat = $rowBooks['seite'];
				$thisBook->numberCat = getNumberCat($rowBooks['nr']);
				$thisBook->itemInVolume = getItemInVolume($rowBooks['nr']);
				$thisBook->bibliographicalLevel = translateLevelEn($rowBooks['qualitaet']);
				$thisBook->titleCat = htmlspecialchars($rowBooks['titel_vorlage']);
				$thisBook->titleBib = htmlspecialchars($rowBooks['titel_bibliographiert']);
				$thisBook->publisher = htmlspecialchars($rowBooks['drucker_verleger']);
				$thisBook->year = $rowBooks['jahr'];
				$thisBook->format = $rowBooks['format'];
				$thisBook->histSubject = $rowBooks['sachgruppe_historisch'];
				$thisBook->subject = $rowBooks['sachbegriff'];
				$thisBook->genre = $rowBooks['gattungsbegriff'];
				$thisBook->mediaType = translateTermsDeEn($rowBooks['medium']);
				$thisBook->manifestation['system'] = $rowBooks['nachweis'];				
				$thisBook->manifestation['id'] = $rowBooks['datensatz'];
				if(strtolower($rowBooks['form']) == 'ungebunden') {
					$thisBook->bound = 0;
				}
				$thisBook->comment = $rowBooks['freitext'];
				$thisBook->digitalCopy = $rowBooks['digital'];
				
				if($thisBook->bibliographicalLevel == 'work' and $thisBook->work['title'] == '') {
					$thisBook->work['title'] = $thisBook->titleBib;
				}
				
				$authorFields = array('autor', 'autor2', 'autor3', 'autor4');
				foreach($authorFields as $field) {
					if($rowBooks[$field]) {
						$person = new person();
						$person->name = htmlspecialchars($rowBooks[$field]);
						$person->role = 'author';
						if($resultAuthors = $db->query('SELECT gnd FROM autor WHERE name LIKE "%'.$rowBooks[$field].'%"')) {
							$person->gnd = trim($resultAuthors->fetch_assoc()['gnd']); 
						}
						$thisBook->persons[] = $person;
					}
				}
				 
				$contributorFields = array('beteiligte_person', 'beteiligte_person2', 'beteiligte_person3', 'beteiligte_person4');
				foreach($contributorFields as $field) {
					if($rowBooks[$field]) {
						$person = new person();
						$person->name = htmlspecialchars($rowBooks[$field]);
						$person->role = 'contributor';
						if($resultContributors = $db->query('SELECT gnd FROM autor WHERE name LIKE "%'.$rowBooks[$field].'%"')) {
							$person->gnd = trim($resultContributors->fetch_assoc()['gnd']); 
						}
						$thisBook->persons[] = $person;
					}
				}
				
				$languageFields = array('sprache', 'sprache2');
				foreach($languageFields as $field) {
					if($rowBooks[$field]) {
						$languageCode = translateGreGrc($rowBooks[$field]);
						$thisBook->language[] = $languageCode;
					}
				}
				
				$placeFields = array('ort', 'ort2');
				foreach($placeFields as $field) {
					if($rowBooks[$field]) {
						$place = new place();
						$place->name = htmlspecialchars($rowBooks[$field]);
						if($resultPlaces = $db->query('SELECT x, y, tgn FROM ort WHERE ort="'.$rowBooks[$field].'"')) {
							$rowGeo = $resultPlaces->fetch_assoc();
							$place->getty = $rowGeo['tgn'];
							$place->geoData['long'] = $rowGeo['x'];
							$place->geoData['lat'] = $rowGeo['y']; 
						}
						$thisBook->places[] = $place;
					}
				}
				$dataArray[] = $thisBook;
		}
	}
	foreach($dataArray as $item) {
		foreach($item->persons as $person) {
			$person->name = replaceArrowBrackets($person->name);
			}
		}	
	return($dataArray);
}
	
function addBeacon($data, $folderName, $keyCat) {
	$beaconString = file_get_contents($folderName.'/beaconStore-'.$keyCat);
	$beaconObject = unserialize($beaconString);
	foreach($data as $item) {
		foreach($item->persons as $person) {
			if($person->gnd != '') {
				foreach($beaconObject->content as $beaconExtract) {
					if(in_array($person->gnd, $beaconExtract->content)) {
						$person->beacon[] = $beaconExtract->key;
					}
				}
			}
		}
	}
	return($data);
}

function translateTermsDeEn($term) {
	$translation = array(
		'Druck' => 'Book', 
		'Handschrift' => 'Manuscript', 
		'Sache' => 'Physical Object', 
		);
	$term = strtr($term, $translation);
	return($term);
}

function translateGreGrc($code) {
	$translation = array('gre' => 'grc');
	$code = strtr($code, $translation);
	return($code);
}
	
function getNumberCat($number) {
	$parts = explode('S', $number);
	if(isset($parts[0])) {
		$number = $parts[0];
	}
	return($number);
}	

function getItemInVolume($number) {
	$parts = explode('S', $number);
	if(isset($parts[1])) {
		return($parts[1]);
	}
	else {
		return('');
	}
}

function translateLevelEn($value) {
	$translation = array(
		'w' => 'work',
		'a' => 'manifestation',
		'e' => 'copy',
		'o' => 'noEvidence');
	$value = strtr($value, $translation);
	return($value);
}	

?>