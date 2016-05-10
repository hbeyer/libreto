<?php

/* 
Die Datei legt einen Ordner für das Projekt an, der die notwendigen Formatierungsdateien enthält.
Dann zieht sie die Daten aus der in $thisCatalogue->database angegebenen Datenbank. Deren Eigenschaften 
liegen in der inhaltsleeren Datei "dropfile.sql" vor.
Die Daten werden in PHP-Objekte umgerechnet, die im Array $data zwischengespeichert werden.
Anschließend wird die Funktion storeBeacon aufgerufen, die in einer Reihe von BEACON-Dateien im Netz 
(definiert in beaconData.php) nach den in $data vorhandenen GND-Nummern sucht. Das Ergebnis wird im 
Projektverzeichnis zwischengespeichert. 
Anschließend werden die gefundenen BEACON-Einträge mit der Funktion addData in die Datensammlung $data 
geschrieben. Diese Datensammlung wird abschließend im Projektverzeichnis unter dem Dateinamen
data-{projektspezifische Endung} serialisiert.
*/

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('encode.php');
include('makeIndex.php');
//include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('storeBeacon.php');
include('setConfiguration.php');

//$thisCatalogue = setConfiguration('bahn');
$thisCatalogue = setConfiguration('rehl');
//$facets = $thisCatalogue->facets;

// Erstelle ein Verzeichnis für das Projekt
$folderName = $thisCatalogue->fileName;
if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
}

// Lade die Daten aus der Datenbank in die Variable $data
$data = load_data_mysql('localhost', 'root', '', $thisCatalogue->database, 'zusammenfassung');

// Suche in BEACON-Dateien nach Einträgen zu den erwähnten Personen und füge diese den Daten hinzu
//storeBeacon($data, $folderName);
$data = addBeacon($data, $folderName);

// Speichere die Daten im Projektverzeichnis
$serialize = serialize($data);
file_put_contents($folderName.'/dataPHP', $serialize);

var_dump($data);

?>