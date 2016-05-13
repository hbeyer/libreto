﻿<?php

function setConfiguration($key) {
	
	$thisCatalogue = new catalogue();
	$thisCatalogue->key = $key;
	
	if($key == 'rehl') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-19-7s/start.htm?image=';
		$thisCatalogue->heading = 'Bibliothek Karl Wolfgang Rehlinger';
		$thisCatalogue->fileName = 'rehlinger';
		$thisCatalogue->database = 'rehlinger';
		$thisCatalogue->title = 'Index Librorvm: Qvos Nobilis Et Ornatissimvs Vir Carolvs VVolfgangvs Relingervs synceri Euangelij ministrorum, Augustæ, vsui liberali sumptu comparauit, ijsq[ue] in omne æuum d.d. secundum altitudinem exemplarium dispositus';
		$thisCatalogue->year = '1575';
		$thisCatalogue->nachweis['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->nachweis['shelfmark'] = 'M: Bc Kapsel 19 (7)';
		$thisCatalogue->GeoBrowserStorageID = '267901';
		$thisCatalogue->listFacets = array('numberCat', 'persName', 'year', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		$thisCatalogue->cloudFacets = array('persName', 'placeName', 'languages', 'publisher');
		$thisCatalogue->doughnutFacets = array('persName', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		return($thisCatalogue);
	}
	elseif($key == 'bahn') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-7-23s/start.htm?image=';
		$thisCatalogue->heading = 'Bibliothek Benedikt Bahnsen';
		$thisCatalogue->fileName = 'bahnsen';
		$thisCatalogue->database = 'bahnsen';
		$thisCatalogue->title = 'Catalogus Variorum, insignium, rarißimorumque tàm Theologicorum, Mathematicorum, Historicorum, Medicorum & Chymicorum, quàm Miscellaneorum, Compactorum & Incompactorum Librorum. Reverend. Dn. Petri Serrarii, Theologi. P.M. Et Experientiss. Dn. Benedicti Bahnsen, Mathemat. P.M. In quâvis Linguâ Hebraîca, Graecâ, Latinâ, Gallicâ & Italicâ scriptorum, Als mede Hoogh en Nederduytsche Boecken, Welcke sullen verkocht worden ... den [...] April 1670 ... / De Catalogen zijn te bekomen ten huyse van Hendrick en Dirck Boom, Boeckverkoopers op de Singel ...';
		$thisCatalogue->year = '1670';
		$thisCatalogue->copy['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->copy['shelfmark'] = 'M: Bc Kapsel 7 (23)';
		$thisCatalogue->GeoBrowserStorageID = '267851';
		$thisCatalogue->listFacets = array('catSubjectFormat', 'persName', 'year', 'subjects', 'genres', 'placeName', 'languages', 'publisher');
		$thisCatalogue->cloudFacets = array('persName', 'subjects', 'histSubject', 'genres', 'placeName', 'languages', 'publisher');
		$thisCatalogue->doughnutFacets = array('persName', 'subjects', 'histSubject', 'genres', 'placeName', 'languages', 'publisher');
		return($thisCatalogue);
	}
	if($key == 'liddel') {
		$thisCatalogue->heading = 'Bibliothek Duncan Liddel';
		$thisCatalogue->fileName = 'liddel';
		$thisCatalogue->database = 'helmstedt';
		$thisCatalogue->title = 'Die Bibliothek des schottischen Mathematikers, Astronomomen und Mediziners Duncan Liddel (1561–1613) wurde von Jane Pierie aus den Beständen der Sir Duncan Rice Library rekonstruiert. Sie enthält ca. 420 Titel in 200 Bänden. Zum großen Teil sammelte Liddel diese zur Zeit seiner Lehrtätigkeit auf dem Kontinent, insbesondere seit 1590 in Helmstedt.';
		$thisCatalogue->year = '1613';
		$thisCatalogue->GeoBrowserStorageID = '272301';
		$thisCatalogue->listFacets = array('shelfmarkOriginal', 'persName', 'subjects', 'languages', 'placeName', 'publisher', 'year', 'systemManifestation');
		$thisCatalogue->cloudFacets = array('shelfmarkOriginal', 'persName', 'subjects', 'languages', 'placeName', 'publisher');
		$thisCatalogue->doughnutFacets = array('persName', 'subjects', 'languages', 'placeName', 'publisher', 'systemManifestation');
		return($thisCatalogue);
	}
	if($key == 'hardt') {
		$thisCatalogue->heading = 'Luthersammlung Hermann von der Hardt';
		$thisCatalogue->fileName = 'luthersammlung';
		$thisCatalogue->description = 'Die Luthersammlung ist benannt nach dem Theologen und Orientalisten Hermann von der Hardt (1660–1746), der als Helmstedter Universitätsbibliothekar bedeutende Erwerbungen an Lutherschriften machte und zudem ein enger Mitarbeiter bei der Luthersammlung von Herzog Rudolf August von Braunschweig-Wolfenbüttel war. Die Sammlung stammt nicht aus Von der Hardts Nachlass, sondern enthält Lutherdrucke Ludwig Rudolphs, die 1704 in die Universitätsbibliothek Helmstedt kamen.';
		$thisCatalogue->year = '1830';
		$thisCatalogue->GeoBrowserStorageID = '324401';
		$thisCatalogue->listFacets = array('shelfmarkOriginal', 'persName', 'year', 'placeName', 'languages', 'publisher', 'format', 'subject');
		$thisCatalogue->cloudFacets = array('shelfmarkOriginal', 'persName', 'placeName', 'languages', 'publisher', 'format', 'subject');
		$thisCatalogue->doughnutFacets = array('persName', 'placeName', 'languages', 'publisher', 'format', 'subject');
		return($thisCatalogue);
	}	
	
}

// Maximale Facettierung
//$thisCatalogue->facets = array('cat', 'persName', 'year', 'subject', 'genre', 'placeName', 'languages', 'publisher', 'format', 'manifestation');

?>