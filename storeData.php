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
include('makeKML.php');
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


//$data = load_data_mysql('localhost', 'root', '', $thisCatalogue->database, 'zusammenfassung');

$dataString = file_get_contents('data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

//storeBeacon($data, $thisCatalogue->key);
//makeKML($data, fileNameTrans($thisCatalogue->heading));

$data = addBeacon($data, $thisCatalogue->key);

$serialize = serialize($data);
file_put_contents('data-'.$thisCatalogue->key, $serialize);


?>