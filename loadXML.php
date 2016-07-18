<?php

function validateXML($path, $pathSchema) {
	$xml = new DOMDocument();
	$xml->load($path);
	if($xml == FALSE) {
		return('Das Dokument ist offenbar nicht wohlgeformt.');
	}
	$valid = $xml->schemaValidate($pathSchema);
	if($valid == TRUE) {
		return(1);
	}
	elseif($valid == FALSE) {
		return('Die Validierung gegen das <a href="'.$pathSchema.'" target="_blank">Schema</a> ist fehlgeschlagen.');
	}
}

function loadXML($path) {
	$xml = new DOMDocument();
	$xml->load($path);
	$resultArray = array();
	$nodeList = $xml->getElementsByTagName('item');
	foreach ($nodeList as $node) {
		$item = makeItemFromNode($node);
		$resultArray[] = $item;
	}
	return($resultArray);
}

function makeItemFromNode($node) {
	$simpleFields = array('id', 'pageCat', 'imageCat', 'numberCat', 'itemInVolume', 'titleCat', 'titleBib', 'titleNormalized', 'publisher', 'year', 'format', 'histSubject', 'mediaType', 'bound', 'comment');
	$multiValuedFields = array('subjects', 'genres', 'languages');
	$subFieldFields = array('manifestation', 'originalItem', 'work');
	$item = new item;
	$children = $node->childNodes;
	foreach($children as $child) {
		$field = strval($child->nodeName);
		if(in_array($field, $simpleFields)) {
			$item->$field = $child->nodeValue;
		}
		elseif(in_array($field, $multiValuedFields)) {
			$item = insertMultiValued($item, $field, $child);
		}
		elseif(in_array($field, $subFieldFields)) {
			$item = insertSubFields($item, $field, $child);
		}
		elseif($field == 'persons') {
			$item = insertPersons($item, $child);
		}
		elseif($field == 'places') {
			$item = insertPlaces($item, $child);			
		}
		unset($field);
	}
	return($item);
}

function insertMultiValued($item, $field, $node) {
	$insert = array();
	$children = $node->childNodes;
	foreach($children as $child) {
		if($child->nodeName != '#text') {
			$insert[] = $child->nodeValue;
		}
	}
	$item->$field = $insert;
	return($item);
}

function insertSubFields($item, $field, $node) {
	$insert = array();
	$children = $node->childNodes;
	foreach($children as $child) {
		if($child->nodeName != '#text') {
			$insert[$child->nodeName] = $child->nodeValue;
		}
	}
	$item->$field = $insert;
	return($item);	
}

function insertPersons($item, $node) {
	$children = $node->childNodes;
	foreach($children as $child) {
		if($child->nodeName != '#text') {
			$person = makePersonFromNode($child);
			$item->persons[] = $person;
		}
	}
	return($item);
}

function insertPlaces($item, $node) {
	$children = $node->childNodes;
	foreach($children as $child) {
		if($child->nodeName != '#text') {
			$place = makePlaceFromNode($child);
			$item->places[] = $place;
		}
	}
	return($item);
}

function makePersonFromNode($node) {
	$properties = array('persName', 'gnd', 'gender', 'role');
	$children = $node->childNodes;
	$person = new person;
	foreach($children as $child) {
		$field = strval($child->nodeName);
		if(in_array($child->nodeName, $properties)) {
			$person->$field = $child->nodeValue;
		}
		elseif($field == 'beacon') {
			$person = insertMultiValued($person, 'beacon', $child);
		}
	}
	return($person);
}

function makePlaceFromNode($node) {
	$properties = array('placeName', 'geoNames', 'gnd', 'getty');
	$children = $node->childNodes;
	$place = new place;
	foreach($children as $child) {
		$field = strval($child->nodeName);
		if(in_array($child->nodeName, $properties)) {
			$place->$field = $child->nodeValue;
		}
		elseif($field == 'geoData') {
			$place = insertSubFields($place, 'geoData', $child);
		}
	}
	return($place);
}

?>