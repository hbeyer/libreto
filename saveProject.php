﻿<?php

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
include('storeBeacon.php');
include('setConfiguration.php');

//$thisCatalogue = setConfiguration('liddel');
$thisCatalogue = setConfiguration('bahn');
//$thisCatalogue = setConfiguration('rehl');
$facets = $thisCatalogue->facets;

//Erstelle ein Verzeichnis für das Projekt (wird momentan vom Skript storeData.php erledigt.
$folderName = fileNameTrans($thisCatalogue->heading);
/*if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
} */

// Erstellt Kopien der proprietären CSS- und JS-Datei im Projektverzeichnis
copy ('proprietary.css', $folderName.'/proprietary.css');
copy ('proprietary.js', $folderName.'/proprietary.js');

// Hole die vom Skript storeData.php zwischengespeicherten Daten aus dem Projektverzeichnis
$dataString = file_get_contents($folderName.'/data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

// Füge die Datasheets für den GeoBrowser dem Projektverzeichnis hinzu (zeitweise aufgehoben)
//makeGeoDataSheet($data, $folderName, 'KML');
//makeGeoDataSheet($data, $folderName, 'CSV');

/* Hier werden die Strukturen (jeweils ein Array aus section-Objekten) gebildet 
und im Array $structures zwischengespeichert.
*/
$structures = array();
include('fieldList.php');
foreach($facets as $facet) {
	if(in_array($facet, $indexFields)) {
		$structure = makeSections($data, $facet);
		if(in_array($facet, $volumeFields)) {
			foreach($structure as $section) {
				$section = makeVolumes($section);
			}
		}
		$structures[] = $structure;
	}
}

// Zu jeder Struktur wird eine Liste mit Kategorien für das Inhaltsverzeichnis berechnet.
$count = 0;
$tocs = array();
foreach($structures as $structure) {
	$tocs[$facets[$count]] = makeToC($structure);
	$count++;
}

// Für jede Struktur wird jetzt eine HTML-Datei berechnet und gespeichert.
$count = 0;
foreach($structures as $structure) {
	$facet = $facets[$count];
	$navigation = makeNavigation($thisCatalogue->heading, $tocs, $facet);
	$content = makeHead($thisCatalogue, $navigation, $facet);
	$content .= makeList($structure, $thisCatalogue);
	$content .= $foot;
	$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->heading).'-'.$facet.'.html';
	$datei = fopen($fileName,"w");
	fwrite($datei, $content, 3000000);
	fclose($datei);
	$count++;	
}

unset($structures);

// Erzeugen der Seite mit den Word Clouds
$navigation = makeNavigation($thisCatalogue->heading, $tocs, 'jqcloud');
$content = makeHead($thisCatalogue, $navigation, 'jqcloud');
$content .= makeCloudPageContent($data, $facets, $folderName);
$content .= $foot;
$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->heading).'-wordCloud.html';
$datei = fopen($fileName,"w");
fwrite($datei, $content, 3000000);
fclose($datei);

// Lösche die von storeBeacon erzeugte temporäre Datei
//unlink($folderName.'/beaconStore-'.$thisCatalogue->key);

?>