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
include('sort.php');
include('encode.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('storeBeacon.php');

$thisCatalogue = new catalogue();


$thisCatalogue->key = 'rehl';
$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-19-7s/start.htm?image=';
$thisCatalogue->heading = 'Bibliothek Karl Wolfgang Rehlingers';
$thisCatalogue->database = 'rehlinger';
$thisCatalogue->title = 'Index Librorvm: Qvos Nobilis Et Ornatissimvs Vir Carolvs VVolfgangvs Relingervs synceri Euangelij ministrorum, Augustæ, vsui liberali sumptu comparauit, ijsq[ue] in omne æuum d.d. secundum altitudinem exemplarium dispositus';
$thisCatalogue->year = '1575';
$thisCatalogue->nachweis['institution'] = 'HAB Wolfenbüttel';
$thisCatalogue->nachweis['shelfmark'] = 'M: Bc Kapsel 19 (7)';

/* $thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-7-23s/start.htm?image=';
$thisCatalogue->key = 'bahn';
$thisCatalogue->heading = 'Bibliothek Benedikt Bahnsens';
$thisCatalogue->database = 'bahnsen';
$thisCatalogue->title = 'Catalogus Variorum, insignium, rarißimorumque tàm Theologicorum, Mathematicorum, Historicorum, Medicorum & Chymicorum, quàm Miscellaneorum, Compactorum & Incompactorum Librorum. Reverend. Dn. Petri Serrarii, Theologi. P.M. Et Experientiss. Dn. Benedicti Bahnsen, Mathemat. P.M. In quâvis Linguâ Hebraîca, Graecâ, Latinâ, Gallicâ & Italicâ scriptorum, Als mede Hoogh en Nederduytsche Boecken, Welcke sullen verkocht worden ... den [...] April 1670 ... / De Catalogen zijn te bekomen ten huyse van Hendrick en Dirck Boom, Boeckverkoopers op de Singel ...';
$thisCatalogue->year = '1670';
$thisCatalogue->copy['institution'] = 'HAB Wolfenbüttel';
$thisCatalogue->copy['shelfmark'] = 'M: Bc Kapsel 7 (23)'; */


// Erstelle ein Verzeichnis für das Projekt
$folderName = fileNameTrans($thisCatalogue->heading);
if(is_dir($folderName) == FALSE) {
	mkdir($folderName, 0700);
}

// Lade die Daten aus der Datenbank in die Variable $data
$data = load_data_mysql('localhost', 'root', '', $thisCatalogue->database, 'zusammenfassung');

// Suche in BEACON-Dateien nach Einträgen
storeBeacon($data, $folderName, $thisCatalogue->key);

// Füge die Information über die Einträge den Daten hinzu
$data = addBeacon($data, $folderName, $thisCatalogue->key);

// Speichere die Daten im Projektverzeichnis
$serialize = serialize($data);
file_put_contents($folderName.'/data-'.$thisCatalogue->key, $serialize);


?>