<?php

function replaceArrowBrackets($string) {
	$translate = array('&lt;' => '', '&gt;' => '', '<' => '', '>' => '', '&amp;lt;' => '', '&amp;gt;' => '');
	$string = strtr($string, $translate);
	return($string);
}

function replaceSlash($string) {
	$translate = array('/' => ' ');
	$string = strtr($string, $translate);
	return($string);
}

function replaceAmp($string) {
	$translate = array('&' => '&#38;');
	$string = strtr($string, $translate);
	return($string);
}
	
function translateAnchor($anchor) {
	$translate = array('Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', ' ' => '', '&' => 'et');
	$anchor = strtr($anchor, $translate);
	return($anchor);
}

function fileNameTrans($fileName) {
	$translation = array('Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss', ' ' => '', '&' => 'Et');
	$fileName = strtr($fileName, $translation);
	return($fileName);
}

function translateFieldNames($field) {
	$translation = array(
		'catSubjectFormat' => 'Katalog', 
		'numberCat' => 'Katalog', 
		'id' => 'Katalog', 
		'shelfmarkOriginal' => 'Signaturen', 		
		'persName' => 'Personen',
		'subjects' => 'Inhalte', 
		'histSubject' => 'Rubriken', 
		'year' => 'Datierung', 
		'placeName' => 'Orte', 
		'languages' => 'Sprachen', 
		'publisher' => 'Drucker', 
		'format' => 'Formate', 
		'mediaType' => 'Materialarten', 
		'systemManifestation' => 'Nachweise', 
		'genres' => 'Gattungen');
	$result = strtr($field, $translation);
	return($result);		
}

function translateFieldNamesButtons($field) {
	$translation = array(
		'shelfmarkOriginal' => 'Signaturen',
		'persName' => 'Personen',
		'subjects' => 'Inhalte', 
		'histSubject' => 'Rubriken',
		'year' => 'Jahre',
		'placeName' => 'Orte', 
		'languages' => 'Sprachen', 
		'publisher' => 'Drucker', 
		'format' => 'Formate', 
		'mediaType' => 'Materialarten', 
		'systemManifestation' => 'Nachweise', 
		'gnd' => 'GND-Nummern',
		'role' => 'Rollen',
		'institutionOriginal' => 'Institutionen',
		'provenanceAttribute' => 'Provenienzmerkmale',
		'genres' => 'Gattungen',
		'bibliographicalLevel' => 'Bibliographische Gattungen'
		);
	$result = strtr($field, $translation);
	return($result);		
}

function translateCheckboxNames($field) {
	$translation = array(
		'pageCat' => 'Seite im Altkatalog',
		'imageCat' => 'Seite im Digitalisat',
		'bibliographicalLevel' => 'Bibliographisches Level',
		'bound' => 'gebunden',
		'gnd' => 'GND-Nummer',
		'titleWork' => 'Werktitel',
		'institutionOriginal' => 'Besitzende Institution',
		'provenanceAttribute' => 'Provenienzmerkmal',
		'catSubjectFormat' => 'Rubrik und Format', 
		'numberCat' => 'Nummer Altkatalog', 
		'id' => 'ID', 
		'shelfmarkOriginal' => 'Signatur', 		
		'persName' => 'Person',
		'subjects' => 'Inhalt', 
		'histSubject' => 'Rubrik', 
		'year' => 'Erscheinungsjahr', 
		'placeName' => 'Ort', 
		'languages' => 'Sprache', 
		'publisher' => 'Drucker', 
		'format' => 'Format', 
		'mediaType' => 'Materialart', 
		'systemManifestation' => 'Nachgewiesen in', 
		'genres' => 'Gattung');
	$result = strtr($field, $translation);
	return($result);		
}


function sortingFormat($format) {
	$pattern = '~^([248])°$~';
	$replacement = '0$1°';
	$format = preg_replace($pattern, $replacement, $format);
	return($format);
}

function reverseSortingFormat($format) {
	$pattern = '~^0([248])°$~';
	$replacement = '$1°';
	$format = preg_replace($pattern, $replacement, $format);
	return($format);
}

function insertSpace($genre) {
	$pattern = '~:([^ ])~';
	$replacement = ': $1';
	$genre = preg_replace($pattern, $replacement, $genre);
	return($genre);
}

function cleanCoordinate($coordinate) {
	$translation = array(',' => '.');
	$coordinate = strtr($coordinate, $translation);
	return($coordinate);
}

function removeSpecial($name) {
	$translation = array('Á' => 'A', 'Ł' => 'L', 'Ǧ' => 'G');
	$name = strtr($name, $translation);
	return($name);	
}

function convertWindowsToUTF8($string) {
  $charset =  mb_detect_encoding($string, "Windows-1252, ISO-8859-1, ISO-8859-15", true);
  $string =  mb_convert_encoding($string, "UTF-8", $charset);
  return $string;
}

function convertUTF8ToWindows($string) {
  $charset =  mb_detect_encoding($string, "UTF-8", true);
  $string =  mb_convert_encoding($string, "Windows-1252", $charset);
  return $string;
}

function convertToWindowsCharset($string) {
  $charset =  mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true);
  $string =  mb_convert_encoding($string, "Windows-1252", $charset);
  return $string;
}

function makeBeaconLink($gnd, $target) {
	$translate = array('{ID}' => $gnd);
	$link = strtr($target, $translate);
	$linkl = urlencode($link);
	return($link);
}

?>