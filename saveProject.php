<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('sort.php');
include('encode.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('storeBeacon.php');

$thisCatalogue = new catalogue();

// Beginn Konfiguration Rehlinger
$thisCatalogue->key = 'rehl';
$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-19-7s/start.htm?image=';
$thisCatalogue->heading = 'Bibliothek Karl Wolfgang Rehlingers';
$thisCatalogue->database = 'rehlinger';
$thisCatalogue->title = 'Index Librorvm: Qvos Nobilis Et Ornatissimvs Vir Carolvs VVolfgangvs Relingervs synceri Euangelij ministrorum, Augustæ, vsui liberali sumptu comparauit, ijsq[ue] in omne æuum d.d. secundum altitudinem exemplarium dispositus';
$thisCatalogue->year = '1575';
$thisCatalogue->nachweis['institution'] = 'HAB Wolfenbüttel';
$thisCatalogue->nachweis['shelfmark'] = 'M: Bc Kapsel 19 (7)';
$facets = array('cat', 'persons', 'year', 'places', 'language', 'publisher', 'format', 'manifestation');
// Ende Konfiguration Rehlinger

// Beginn Konfiguration Bahnsen
/* $thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-7-23s/start.htm?image=';
$thisCatalogue->key = 'bahn';
$thisCatalogue->heading = 'Bibliothek Benedikt Bahnsens';
$thisCatalogue->database = 'bahnsen';
$thisCatalogue->title = 'Catalogus Variorum, insignium, rarißimorumque tàm Theologicorum, Mathematicorum, Historicorum, Medicorum & Chymicorum, quàm Miscellaneorum, Compactorum & Incompactorum Librorum. Reverend. Dn. Petri Serrarii, Theologi. P.M. Et Experientiss. Dn. Benedicti Bahnsen, Mathemat. P.M. In quâvis Linguâ Hebraîca, Graecâ, Latinâ, Gallicâ & Italicâ scriptorum, Als mede Hoogh en Nederduytsche Boecken, Welcke sullen verkocht worden ... den [...] April 1670 ... / De Catalogen zijn te bekomen ten huyse van Hendrick en Dirck Boom, Boeckverkoopers op de Singel ...';
$thisCatalogue->year = '1670';
$thisCatalogue->copy['institution'] = 'HAB Wolfenbüttel';
$thisCatalogue->copy['shelfmark'] = 'M: Bc Kapsel 7 (23)'; 
$facets = array('cat', 'persons', 'year', 'subject', 'genre', 'places', 'language', 'publisher'); */
// Ende Konfiguration Bahnsen


// Maximale Facettierung
//$facets = array('cat', 'persons', 'year', 'subject', 'genre', 'places', 'language', 'publisher', 'format', 'manifestation');

//Erstelle ein Verzeichnis für das Projekt (wird momentan vom Skript storeData.php erledigt.
$folderName = fileNameTrans($thisCatalogue->heading);
/*if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
} */

// Erstellt Kopien der proprietären CSS- und JS-Datei im Projektverzeichnis
copy ('proprietary.css', $folderName.'/proprietary.css');
copy ('jsfunctions.js', $folderName.'/jsfunctions.js');

// Hole die vom Skript storeData.php zwischengespeicherten Daten aus dem Projektverzeichnis
$dataString = file_get_contents($folderName.'/data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

// Füge die Datasheets für den GeoBrowser dem Projektverzeichnis hinzu
makeGeoDataSheet($data, $folderName, 'KML');
makeGeoDataSheet($data, $folderName, 'CSV');

/* Weil einige Facetten zusätzliche Berechnungen erfordern (z. B. die Anfangsbuchstaben der Autoren), 
wird die Listenstruktur teils von speziellen Funktionen, teils von der allgemeinen Funktion makeSections 
übernommen. Die folgende Funktion odnet jeweils einer Facette die richtige Funktion zu.
*/
function makeSectionsByFacet($data, $facet) {
	$structuredData = array();
	$easyFacets = array('subject', 'histSubject', 'places', 'language', 'publisher', 'format', 'mediaType', 'genre', 'manifestation');
	if($facet == 'cat') {
		$structuredData = makeSectionsCat($data);
	}
	elseif($facet == 'persons') {
		$structuredData = makeSectionsAuthor($data);
	}
	elseif($facet == 'year') {
		$structuredData = makeSectionsYear($data);
	}
	elseif(in_array($facet, $easyFacets)) {
		$structuredData = makeSections($data, $facet);
	}
	return($structuredData);
}

/* Hier werden die Strukturen (jeweils ein Array aus section-Objekten) gebildet 
und im Array $structures zwischengespeichert.
*/
$structures = array();
foreach($facets as $facet) {
	$structures[] = makeSectionsByFacet($data, $facet);
}
unset($data);

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
	$content = makeHead($thisCatalogue->heading, $thisCatalogue->year, $thisCatalogue->title, $navigation);
	$content .= makeList($structure, $thisCatalogue);
	$content .= $foot;
	$fileName = fileNameTrans($folderName.'/'.$thisCatalogue->heading).'-'.$facet.'.html';
	$datei = fopen($fileName,"w");
	fwrite($datei, $content, 3000000);
	fclose($datei);
	$count++;	
}

// Lösche die von storeBeacon erzeugte temporäre Datei
//unlink($folderName.'/beaconStore-'.$thisCatalogue->key);

?>