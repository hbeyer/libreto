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
		'subject' => 'Inhalte', 
		'histSubject' => 'Rubriken', 
		'year' => 'Datierung', 
		'placeName' => 'Orte', 
		'language' => 'Sprachen', 
		'publisher' => 'Drucker', 
		'format' => 'Formate', 
		'mediaType' => 'Materialarten', 
		'systemManifestation' => 'Nachweise', 
		'genre' => 'Gattungen');
	$result = strtr($field, $translation);
	return($result);		
}

function translateFieldNamesButtons($field) {
	$translation = array(
		'shelfmarkOriginal' => 'Signaturen',
		'persName' => 'Personen',
		'subject' => 'Inhalte', 
		'histSubject' => 'Rubriken',
		'year' => 'Jahre',
		'placeName' => 'Orte', 
		'language' => 'Sprachen', 
		'publisher' => 'Drucker', 
		'format' => 'Formate', 
		'mediaType' => 'Materialarten', 
		'systemManifestation' => 'Nachweise', 
		'gnd' => 'GND-Nummern',
		'role' => 'Rollen',
		'institutionOriginal' => 'Institutionen',
		'provenanceAttribute' => 'Provenienzmerkmale',
		'genre' => 'Gattungen',
		'bibliographicalLevel' => 'Bibliographische Gattungen'
		);
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

?>