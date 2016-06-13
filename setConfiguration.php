<?php

function setConfiguration($key) {
	
	$thisCatalogue = new catalogue();
	$thisCatalogue->key = $key;
	
	if($key == 'rehl') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-19-7s/start.htm?image=';
		$thisCatalogue->owner = 'Rehlinger, Carl Wolfgang';
		$thisCatalogue->ownerGND = '1055708286';
		$thisCatalogue->heading = 'Bibliothek Carl Wolfgang Rehlinger';
		$thisCatalogue->fileName = 'rehlinger';
		$thisCatalogue->database = 'rehlinger';
		$thisCatalogue->title = 'Index Librorvm: Qvos Nobilis Et Ornatissimvs Vir Carolvs VVolfgangvs Relingervs synceri Euangelij ministrorum, Augustæ, vsui liberali sumptu comparauit, ijsq[ue] in omne æuum d.d. secundum altitudinem exemplarium dispositus';
		$thisCatalogue->year = '1575';
		$thisCatalogue->copy['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->copy['shelfmark'] = 'M: Bc Kapsel 19 (7)';
		$thisCatalogue->GeoBrowserStorageID = '267901';
		$thisCatalogue->listFacets = array('numberCat', 'persName', 'year', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		$thisCatalogue->cloudFacets = array('persName', 'placeName', 'languages', 'publisher');
		$thisCatalogue->doughnutFacets = array('persName', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		return($thisCatalogue);
	}
	elseif($key == 'bahn') {
		$thisCatalogue->base = 'http://diglib.hab.de/drucke/bc-kapsel-7-23s/start.htm?image=';
		$thisCatalogue->owner = 'Bahnsen, Benedikt';
		$thisCatalogue->ownerGND = '128989289';
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
		$thisCatalogue->owner = 'Liddel, Duncan';
		$thisCatalogue->ownerGND = '117671622';
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
		$thisCatalogue->owner = 'Herzog August Bibliothek Wolfenbüttel';
		$thisCatalogue->ownerGND = '8989-8';
		$thisCatalogue->heading = 'Luthersammlung Hermann von der Hardt';
		$thisCatalogue->fileName = 'luthersammlung';
		$thisCatalogue->description = 'Diese Luthersammlung ist benannt nach dem Theologen und Orientalisten Hermann von der Hardt (1660–1746), der ein enger Mitarbeiter von Herzog Rudolf August von Braunschweig-Wolfenbüttel war und ihn bei seiner Sammlung von Reformationsschriften unterstützte. 1703 kam die Sammlung Rudolf Augusts in die Universitätsbibliothek Helmstedt. Im Katalog der Helmstedter Druckschriften aus dem späten 18. Jahrhundert sind sie unter dem Buchstaben F ("Lutheri scripta") aufgeführt. Diese Lutherschriften kamen 1815 (Quartbände) und 1828 (übrige Bände) nach Wolfenbüttel. Dort wurden sie unter dem Bibliothekar Karl Philipp Christian Schönemann (amtierte 1830–1854) teilweise neu gebunden, vermutlich kamen neue Schriften aus aufgelösten Sammelbänden hinzu.';
		$thisCatalogue->year = '1850';
		$thisCatalogue->GeoBrowserStorageID = '324401';
		$thisCatalogue->listFacets = array('shelfmarkOriginal', 'persName', 'year', 'placeName', 'languages', 'publisher', 'format', 'subject');
		$thisCatalogue->cloudFacets = array('shelfmarkOriginal', 'persName', 'placeName', 'languages', 'publisher', 'format', 'subject');
		$thisCatalogue->doughnutFacets = array('persName', 'placeName', 'languages', 'publisher', 'format', 'subject');
		return($thisCatalogue);
	}
	if($key == 'antoinette') {
		$thisCatalogue->owner = 'Antoinette Amalie von Braunschweig-Wolfenbüttel';
		$thisCatalogue->ownerGND = '141678615';
		$thisCatalogue->heading = 'Bibliothek der Herzogin Antoinette Amalie';
		$thisCatalogue->fileName = 'antoinette';
		$thisCatalogue->description = 'Herzogin Antoinette Amalie zu Braunschweig und Lüneburg (1696–1762) war die jüngste Tochter von Herzog Ludwig Rudolf von Braunschweig-Wolfenbüttel sowie seit 1712 die Gemahlin Herzog  Ferdinand Albrechts II. von Braunschweig-Bevern, den sie um 27 Jahre überlebte. Ihre Bibliothek im Umfang von 1.313 Drucken und 28 Handschriften gelangte wie viele andere fürstliche Privatbibliotheken dank der aktiven Erwerbungspolitik Herzog Carls I. in die Wolfenbütteler Bibliothek, wo sie Teil der Mittleren Aufstellung ist.';
		$thisCatalogue->year = '1762';
		$thisCatalogue->nachweis['institution'] = 'HAB Wolfenbüttel';
		$thisCatalogue->nachweis['shelfmark'] = 'BA, I, 631';
		$thisCatalogue->GeoBrowserStorageID = '';
		$thisCatalogue->listFacets = array('numberCat', 'persName', 'year', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		$thisCatalogue->cloudFacets = array('persName', 'placeName', 'languages', 'publisher');
		$thisCatalogue->doughnutFacets = array('persName', 'placeName', 'languages', 'publisher', 'format', 'systemManifestation');
		return($thisCatalogue);
	}
	
}

?>