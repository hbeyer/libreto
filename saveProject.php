<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('encode.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('makeCloudList.php');
include('makeDoughnutList.php');
include('storeBeacon.php');
include('setConfiguration.php');
include('makeCSV.php');
include('makeXML.php');

//$thisCatalogue = setConfiguration('rehl');
//$thisCatalogue = setConfiguration('bahn');
$thisCatalogue = setConfiguration('liddel');
//$thisCatalogue = setConfiguration('hardt');
$facets = $thisCatalogue->listFacets;
$cloudFacets = $thisCatalogue->cloudFacets;
$doughnutFacets = $thisCatalogue->doughnutFacets;

//Erstelle ein Verzeichnis für das Projekt (wird momentan vom Skript storeData.php erledigt.
$folderName = fileNameTrans($thisCatalogue->fileName);
/* if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
} */

// Erstellt Kopien der proprietären CSS- und JS-Datei im Projektverzeichnis
copy ('proprietary.css', $folderName.'/proprietary.css');
copy ('proprietary.js', $folderName.'/proprietary.js');
copy ('chart.js', $folderName.'/chart.js');

// Hole die vom Skript storeData.php zwischengespeicherten Daten aus dem Projektverzeichnis
$dataString = file_get_contents($folderName.'/dataPHP');
$data = unserialize($dataString);
unset($dataString);

// Füge die Datasheets für den GeoBrowser dem Projektverzeichnis hinzu (zeitweise aufgehoben)
//makeGeoDataSheet($data, $folderName, 'KML');
//makeGeoDataSheet($data, $folderName, 'CSV');

// Export als CSV- und XML-Datei
//makeCSV($data, $folderName);
//saveXML($data, $thisCatalogue, $folderName);

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
				$section = joinVolumes($section);
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
	$navigation = makeNavigation($thisCatalogue->fileName, $tocs, $facet);
	$content = makeHead($thisCatalogue, $navigation, $facet);
	$content .= makeList($structure, $thisCatalogue, $folderName);
	$content .= $foot;
	$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->fileName).'-'.$facet.'.html';
	$datei = fopen($fileName,"w");
	fwrite($datei, $content, 3000000);
	fclose($datei);
	$count++;	
}

unset($structures);

// Erzeugen der Seite mit den Word Clouds
$navigation = makeNavigation($thisCatalogue->fileName, $tocs, 'jqcloud');
$content = makeHead($thisCatalogue, $navigation, 'jqcloud');
$content .= makeCloudPageContent($data, $cloudFacets, $folderName);
$content .= $foot;
$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->fileName).'-wordCloud.html';
$datei = fopen($fileName,"w");
fwrite($datei, $content, 3000000);
fclose($datei);

// Erzeugen der Seite mit den Doughnut Charts
$navigation = makeNavigation($thisCatalogue->fileName, $tocs, 'doughnut');
$content = makeHead($thisCatalogue, $navigation, 'doughnut');
$content .= makeDoughnutPageContent($data, $doughnutFacets, $folderName);
$content .= $foot;
$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->fileName).'-doughnut.html';
$datei = fopen($fileName,"w");
fwrite($datei, $content, 3000000);
fclose($datei);

// Lösche die von storeBeacon erzeugte temporäre Datei
//unlink($folderName.'/beaconStore-'.$thisCatalogue->key);

?>