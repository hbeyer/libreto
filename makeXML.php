<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('encode.php');
include('parseDOM.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('makeCloudList.php');
include('makeDoughnutList.php');
include('storeBeacon.php');
include('setConfiguration.php');

//$thisCatalogue = setConfiguration('rehl');
$thisCatalogue = setConfiguration('bahn');
//$thisCatalogue = setConfiguration('liddel');
$facets = $thisCatalogue->listFacets;
$cloudFacets = $thisCatalogue->cloudFacets;
$doughnutFacets = $thisCatalogue->doughnutFacets;

//Erstelle ein Verzeichnis für das Projekt (wird momentan vom Skript storeData.php erledigt.
$folderName = fileNameTrans($thisCatalogue->fileName);
/* if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
} */

// Hole die vom Skript storeData.php zwischengespeicherten Daten aus dem Projektverzeichnis
$dataString = file_get_contents($folderName.'/data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

function saveXML($data, $catalogue, $folderName) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$rootElement = $dom->createElement('collection');
	foreach($data as $item) {
		$itemElement = $dom->createElement('item');
		$itemElement = fillDOMItem($itemElement, $item, $dom);
		$rootElement->appendChild($itemElement);
	}
	$dom->appendChild($rootElement);
	$result = $dom->saveXML();
	$handle = fopen($folderName.'/'.$catalogue->fileName.'.xml', "w");
	fwrite($handle, $result, 3000000);
}

function fillDOMItem($itemElement, $item, $dom) {
	foreach($item as $key => $value) {
		// Fall 1: Variable ist ein einfacher Wert
		if(is_array($value) == FALSE) {
			$value = replaceAmp($value);
			$itemProperty = $dom->createElement($key, $value);
			$itemElement->appendChild($itemProperty);
		}
		//Fall 2: Variable ist ein Array
		elseif(isset($value[0])) {
			//Fall 2.1: Variable ist ein Array aus einfachen Werten
			if(is_string($value[0]) or is_integer($value[0])) {
				$separatedString = implode(';', $value);
				$itemArrayProperty = $dom->createElement($key, $separatedString);
				$itemElement->appendChild($itemArrayProperty);
			}
			//Fall 2.2: Variable ist ein Array aus Objekten
			elseif(is_object($value[0])) {
				$itemObjectProperty = $dom->createElement($key);
				foreach($value as $object) {
					$nameObject = get_class($object);
					$objectElement = $dom->createElement($nameObject);
					foreach($object as $objectKey => $objectValue) {
						//Fall 2.2.1: Variable im Objekt ist ein Array
						if(is_array($objectValue)) {
							$objectVariable = $dom->createElement($objectKey);
							$collectNonAssociative = array();
							foreach($objectValue as $arrayKey => $arrayValue) {
								//Fall 2.2.1.1: Variable im Objekt ist ein assoziatives Array
								if(is_string($arrayKey)) {
									$objectArrayElement = $dom->createElement($arrayKey, $arrayValue);
									$objectVariable->appendChild($objectArrayElement);
								}
								//Fall 2.2.1.2: Variable im Objekt ist ein numerisches Array
								elseif(is_int($arrayKey)) {
									$collectNonAssociative[] = $arrayValue;
								}
							}
							if(isset($collectNonAssociative[0])) {
								$separatedList = implode(';', $collectNonAssociative);
								$objectVariable = $dom->createElement($objectKey, $separatedList);
							}
							$objectElement->appendChild($objectVariable);
						}
						//Fall 2.2.2: Variable im Objekt ist ein Integer oder String
						elseif(is_int($objectValue) or is_string($objectValue)) {
							$objectVariable = $dom->createElement($objectKey, $objectValue);
							$objectElement->appendChild($objectVariable);
						}
					}
					$itemObjectProperty->appendChild($objectElement);
				}
				$itemElement->appendChild($itemObjectProperty);
			}
		}
	}
	return($itemElement);
}

//var_dump($data);
saveXML($data, $thisCatalogue, $folderName);

?>