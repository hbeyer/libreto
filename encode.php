<?php

function replaceArrowBrackets($string) {
	$translate = array('&lt;' => '', '&gt;' => '', '<' => '', '>' => '');
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
		'cat' => 'Katalog', 
		'persons' => 'Personen', 
		'subject' => 'Inhalt', 
		'histSubject' => 'alter Klassifikation', 
		'year' => 'Jahr', 
		'places' => 'Ort', 
		'language' => 'Sprache', 
		'publisher' => 'Drucker', 
		'format' => 'Format', 
		'mediaType' => 'Materialart', 
		'manifestation' => 'Nachweis', 
		'genre' => 'Gattung');
	$result = strtr($field, $translation);
	return($result);		
}

function sortingFormat($format) {
	$translation = array('2°' => '02°', '4°' => '04°', '8°' => '08°');
	$format = strtr($format, $translation);
	return($format);
}

function reverseSortingFormat($format) {
	$translation = array('02°' => '2°', '04°' => '4°', '08°' => '8°');
	$format = strtr($format, $translation);
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

?>