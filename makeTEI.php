<?php

function makeTEI($data, $folder, $fileName, $catalogue) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$dom->load('templateTEI.xml');
	insertMetadata($dom, $catalogue);
	insertPersonList($dom, $data);	
	insertPlaceList($dom, $data);	
	$xml = $dom->saveXML();
	$handle = fopen($folder.'/'.$fileName.'-tei.xml', 'w');
	fwrite($handle, $xml, 3000000);
}

function insertMetadata($dom, $catalogue) {
	$titleNodeList = $dom->getElementsByTagName('title');
	$title = $titleNodeList->item(0);
	$headingText = $catalogue->heading;
	if($catalogue->year) {
		$headingText .= ' ('.$catalogue->year.')';
	}
	$heading = $dom->createTextNode($headingText);
	$title->appendChild($heading);
}

function insertPersonList($dom, $data) {
	$personIndex = makeIndex($data, 'persName');
	$personList = $dom->createElement('listPerson');
	$xmlIDs = array();
	foreach($personIndex as $entry) {
		$xmlID = $entry->label;
		$xmlID = encodeXMLID($xmlID);
		//Prüfen, ob ein Eintrag mit dieser ID schon angelegt wurde.
		if(in_array($xmlID, $xmlIDs) == FALSE) {
			$xmlIDs[] = $xmlID;
		}
		//Wenn ein entsprechender Eintrag existiert, wird davon ausgegangen, dass beide identisch sind.
		else {
			continue;
		}
		if($entry->authority['system'] == 'gnd' and $entry->authority['id'] != '') {
			$keyValue = 'gnd_'.$entry->authority['id'];
		}
		else {
			$keyValue = $xmlID;
		}
		$listEntry = $dom->createElement('person');
		$xmlIDAttr = $dom->createAttribute('xml:id');
		$xmlIDAttr->value = $xmlID;
		$listEntry->appendChild($xmlIDAttr);
		$persName = $dom->createElement('persName');
		$key = $dom->createAttribute('key');
		$key->value = $keyValue;
		$persName->appendChild($key);
		$name = $dom->createTextNode($entry->label);
		$persName->appendChild($name);
		$listEntry->appendChild($persName);
		$personList->appendChild($listEntry);
	}
	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$body->appendChild($personList);
	return($dom);
}

function insertPlaceList($dom, $data) {
	$placeIndex = makeIndex($data, 'placeName');
	$placeList = $dom->createElement('listPlace');
	foreach($placeIndex as $entry) {
		$xmlID = $entry->label;
		$xmlID = encodeXMLID($xmlID);
		$keyValue = '';
		if($entry->authority['id'] != '') {
			$keyValue = $entry->authority['system'].'_'.$entry->authority['id'];
		}
		$listEntry = $dom->createElement('place');
		$xmlIDAttr = $dom->createAttribute('xml:id');
		$xmlIDAttr->value = $xmlID;
		$listEntry->appendChild($xmlIDAttr);
		$placeName = $dom->createElement('placeName');
		if($keyValue != '') {
			$key = $dom->createAttribute('key');
			$key->value = $keyValue;
			$placeName->appendChild($key);
		}
		$name = $dom->createTextNode($entry->label);
		$placeName->appendChild($name);
		$listEntry->appendChild($placeName);
		$placeList->appendChild($listEntry);
	}
	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$body->appendChild($placeList);
	return($dom);
}

function insertEntries($data, $dom) {
	
}

?>