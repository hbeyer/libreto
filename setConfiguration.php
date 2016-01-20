<?php

function setConfiguration($key) {
	
	$thisCatalogue = new catalogue();
	$thisCatalogue->key = $key;
	
	if($key == 'rehl') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-19-7s/start.htm?image=';
		$thisCatalogue->heading = 'Bibliothek Karl Wolfgang Rehlingers';
		$thisCatalogue->database = 'rehlinger';
		$thisCatalogue->title = 'Index Librorvm: Qvos Nobilis Et Ornatissimvs Vir Carolvs VVolfgangvs Relingervs synceri Euangelij ministrorum, Augustæ, vsui liberali sumptu comparauit, ijsq[ue] in omne æuum d.d. secundum altitudinem exemplarium dispositus';
		$thisCatalogue->year = '1575';
		$thisCatalogue->nachweis['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->nachweis['shelfmark'] = 'M: Bc Kapsel 19 (7)';
		$thisCatalogue->GeoBrowserStorageID = '267901';
		$thisCatalogue->facets = array('cat', 'persName', 'year', 'placeName', 'language', 'publisher', 'format', 'manifestation');
		return($thisCatalogue);
	}
	elseif($key == 'bahn') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-7-23s/start.htm?image=';
		$thisCatalogue->heading = 'Bibliothek Benedikt Bahnsens';
		$thisCatalogue->database = 'bahnsen';
		$thisCatalogue->title = 'Catalogus Variorum, insignium, rarißimorumque tàm Theologicorum, Mathematicorum, Historicorum, Medicorum & Chymicorum, quàm Miscellaneorum, Compactorum & Incompactorum Librorum. Reverend. Dn. Petri Serrarii, Theologi. P.M. Et Experientiss. Dn. Benedicti Bahnsen, Mathemat. P.M. In quâvis Linguâ Hebraîca, Graecâ, Latinâ, Gallicâ & Italicâ scriptorum, Als mede Hoogh en Nederduytsche Boecken, Welcke sullen verkocht worden ... den [...] April 1670 ... / De Catalogen zijn te bekomen ten huyse van Hendrick en Dirck Boom, Boeckverkoopers op de Singel ...';
		$thisCatalogue->year = '1670';
		$thisCatalogue->copy['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->copy['shelfmark'] = 'M: Bc Kapsel 7 (23)';
		$thisCatalogue->GeoBrowserStorageID = '267851';
		$thisCatalogue->facets = array('cat', 'persName', 'year', 'subject', 'genre', 'placeName', 'language', 'publisher');
		return($thisCatalogue);
	}
	
}

// Maximale Facettierung
//$thisCatalogue->facets = array('cat', 'persName', 'year', 'subject', 'genre', 'placeName', 'language', 'publisher', 'format', 'manifestation');

?>