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
				$thisBook->titleCat = htmlspecialchars($rowBooks['titel_vorlage']);
				$thisBook->titleBib = htmlspecialchars($rowBooks['titel_bibliographiert']);
				$thisBook->publisher = htmlspecialchars($rowBooks['drucker_verleger']);
				$thisBook->year = $rowBooks['jahr'];
				$thisBook->format = $rowBooks['format'];
				$thisBook->histSubject = $rowBooks['sachgruppe_historisch'];
				$thisBook->subjects[] = $rowBooks['sachbegriff'];
				$thisBook->genres[] = $rowBooks['gattungsbegriff'];
				$thisBook->mediaType = translateTermsDeEn($rowBooks['medium']);
				$thisBook->manifestation['systemManifestation'] = $rowBooks['nachweis'];				
				$thisBook->manifestation['idManifestation'] = $rowBooks['datensatz'];
				if(strtolower($rowBooks['form']) == 'ungebunden') {
					$thisBook->bound = 0;
				}
				$thisBook->comment = $rowBooks['freitext'];
				$thisBook->digitalCopy = $rowBooks['digital'];
				
				$authorFields = array('autor', 'autor2', 'autor3', 'autor4');
				foreach($authorFields as $field) {
					if($rowBooks[$field]) {
						$person = new person();
						$person->persName = htmlspecialchars($rowBooks[$field]);
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
						$person->persName = htmlspecialchars($rowBooks[$field]);
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
						$thisBook->languages[] = $languageCode;
					}
				}
				
				preg_match('~[Ww]eitere Sprache: ([a-z]{3})~', $rowBooks['freitext'], $matches);
				if(isset($matches[1])) {
					$thisBook->languages[] = $matches[1];
				}
				
				$placeFields = array('ort', 'ort2');
				foreach($placeFields as $field) {
					if($rowBooks[$field]) {
						$place = new place();
						$place->placeName = htmlspecialchars($rowBooks[$field]);
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
			$person->persName = replaceArrowBrackets($person->persName);
			}
		}	
	return($dataArray);
}

function load_data_liddel($server, $user, $password, $database, $table) {
	$db = new mysqli($server, $user, $password, $database);
	$db->set_charset("utf8");
	if (mysqli_connect_errno()) {
		die ('Konnte keine Verbindung zur Datenbank aufbauen: '.mysqli_connect_error().'('.mysqli_connect_errno().')');
	}
	
	$archive = new GeoDataArchive();
	$archive->loadFromFile();
	
	$translatePlaces = array('Görlitz (Dresden)' => 'Görlitz', 'Middleburg' => 'Middelburg', 'Neustadt an der Haardt' => 'Neustadt an der Weinstraße');
	
	if($result = $db->query('SELECT * FROM '.$table)) {
		$dataArray = array();
		while($rowBooks = $result->fetch_assoc()) {
			$rowBooks = array_map('trim', $rowBooks);
			$thisBook = new item();
			$thisBook->id = $rowBooks['system_no'];
			$thisBook->titleBib = htmlspecialchars($rowBooks['title']);
			$thisBook->publisher = htmlspecialchars($rowBooks['printer']);
			$thisBook->year = $rowBooks['date'];
			$thisBook->subjects[] = $rowBooks['subject'];
			$thisBook->mediaType = 'Book';
			$thisBook->originalItem['institutionOriginal'] = 'Aberdeen, Sir Duncan Rice Library';				
			$thisBook->originalItem['shelfmarkOriginal'] = $rowBooks['shelfmark'];
			$thisBook->originalItem['targetOPAC'] = 'https://aulib.abdn.ac.uk/F?func=direct&local_base=ABN01&doc_number={ID}';
			$thisBook->originalItem['searchID'] = $rowBooks['system_no'];
			$thisBook->languages = explode(';', $rowBooks['language']);
			
			$placeName = $rowBooks['place_ger'];
			if($placeName == '') {
				$placeName = $rowBooks['place'];
			}
			
			$placeName = strtr($placeName, $translatePlaces);
			
			$placeNameSearch = $placeName;
			
			if($placeNameSearch == 'Frankfurt' or $placeNameSearch == 'Frankfurt am Main') {
				$placeNameSearch = 'Frankfurt/M.';
				$placeName = 'Frankfurt am Main';
			}
			elseif($placeNameSearch == 'Frankfurt an der Oder' or $placeNameSearch == 'Frankfurt (Oder)') {
				$placeNameSearch = 'Frankfurt/O.';
			}
			
			
			$place = new place();
			$place->placeName = $placeName;
			$placeFromArchive = $archive->getByName($placeNameSearch);
			if($placeFromArchive) {
				$place->geoData['lat'] = $placeFromArchive->lat;
				$place->geoData['long'] = $placeFromArchive->long;
			}
			
			$thisBook->places[] = $place;
			
			if(preg_match('~VD[ ]?(1[67]) (.+)~', $rowBooks['vd'], $matches)) {
				$thisBook->manifestation['systemManifestation'] = 'VD'.$matches[1];
				$thisBook->manifestation['idManifestation'] = $matches[2];
			}
			elseif($rowBooks['retrieve_from'] == 'GBV') {
				$thisBook->manifestation['systemManifestation'] = 'GBV';
				$thisBook->manifestation['idManifestation'] = $rowBooks['ppn'];
			}
			
			if($rowBooks['author'] != '') {
				$person = new person();
				$person->persName = $rowBooks['author'];
				$person->role = 'author';
				if($rowBooks['gnd']) {
					if($resultAuthor = $db->query('SELECT * FROM liddel_authority WHERE gnd_aut LIKE "'.$rowBooks['gnd'].'"')) {
						$rowPerson = $resultAuthor->fetch_assoc();
						$person->persName = $rowPerson['name_de'];
						if($person->persName == '') {
							$person->persName = $rowPerson['name_en'];
						}
						$person->gnd = $rowPerson['gnd_aut'];
					}
				}
				$thisBook->persons[] = $person;
			}
			if($resultDigi = $db->query('SELECT * FROM liddel_library_digi WHERE id_print LIKE '.$rowBooks['id'])) {
				while($rowDigi = $resultDigi->fetch_assoc()) {
					$thisBook->digitalCopy = $rowDigi['value'];
				}
			}
			$dataArray[] = $thisBook;
		}
	}
	foreach($dataArray as $item) {
		foreach($item->persons as $person) {
			$person->perName = replaceArrowBrackets($person->persName);
		}
	}	
	return($dataArray);
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
		$number = intval($parts[0]);
	}
	return($number);
}	
function getItemInVolume($number) {
	$parts = explode('S', $number);
	if(isset($parts[1])) {
		return(intval($parts[1]));
	}
	else {
		return(0);
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
