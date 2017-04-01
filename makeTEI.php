<?php

/*
Ausstehend in diesem Skript:
Regelung für die Werkebene bzw. für nicht identifizierte Titel
Klammern von Sammelbänden
*/

function makeTEI($data, $folder, catalogue $catalogue) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$dom->load('templateTEI.xml');
	insertMetadata($dom, $catalogue);
	insertTranscription($dom, $data, $catalogue);
	insertBibliography($dom, $data, $catalogue);
	$xml = $dom->saveXML();
	$handle = fopen($folder.'/'.$catalogue->fileName.'-tei.xml', 'w');
	fwrite($handle, $xml, 3000000);
}

function insertMetadata($dom, $catalogue) {
	
	// Insert title of the reconstructed library
	$titleNodeList = $dom->getElementsByTagName('title');
	$title = $titleNodeList->item(0);
	$headingText = $catalogue->heading;
	if($catalogue->year) {
		$headingText .= ' ('.$catalogue->year.')';
	}
	$heading = $dom->createTextNode($headingText);
	$title->appendChild($heading);
	
	// Insert date of reconstruction
	$dateNodeList = $dom->getElementsByTagName('date');
	$date = $dateNodeList->item(0);
	$year = $dom->createTextNode(date('Y'));
	$date->appendChild($year);
	$date->setAttribute('when', date('Y-m-d'));
	
	// Insert source information from catalogue object
	$listWitList = $dom->getElementsByTagName('listWit');
	$listWit = $listWitList->item(0);
	$witness = $dom->createElement('witness');
	$witness->setAttribute('xml:id', 'witness_0');
	$textWitness = $dom->createTextNode($catalogue->institution.', '.$catalogue->shelfmark);
	$witness->appendChild($textWitness);
	$listWit->appendChild($witness);
}

function insertTranscription($dom, $data, $catalogue) {
	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$count = 0;
	$lastHistSubject = '';
	$lastPageCat = '';

	foreach($data as $item) {
	
		//Wenn eine neue Seite beginnt, Pagebreak einfügen
		$pageBreak = NULL;
		if(trim(strtolower($item->pageCat)) != trim(strtolower($lastPageCat))) {
			$pageBreak = $dom->createElement('pb');
			$pageBreak->setAttribute('n', $item->pageCat);
			if($item->imageCat) {
				$pageBreak->setAttribute('facs', $catalogue->base.$item->imageCat);
			}
		}
		
		//Wenn ein neuer Abschnitt beginnt neue listBibl anlegen
		if(trim(strtolower($item->histSubject)) != trim(strtolower($lastHistSubject))) {
			//Wenn ein alter Abschnitt vorhergegangen ist, wird der Inhalt in body eingefügt
			if(isset($listBibl)) {
				$body->appendChild($listBibl);
			}
			$listBibl = $dom->createElement('listBibl');
			$listBibl->setAttribute('type', 'transcription');
			$histSubjectText = $dom->createTextNode($item->histSubject);
			$histSubject = $dom->createElement('head');
			$histSubject->appendChild($histSubjectText);
			//Insert a pagebreak at the begin of listBibl (i. e. before head)
			if(isset($pageBreak)) {
				$listBibl->appendChild($pageBreak);
				$pageBreak = NULL;
			}
			$listBibl->appendChild($histSubject);
		}
	
		// Insert a bibl element for each catalogue entry
		$bibl = $dom->createElement('bibl');
		if($item->numberCat) {
			$bibl->setAttribute('n', $item->numberCat);
		}
		$id = assignID($item->id, $count, $catalogue->fileName);
		$bibl->setAttribute('xml:id', $id);
		unset($id);
		if($item->titleCat) {
			//Avoid &amp;amp;
			$titleCatText = html_entity_decode($item->titleCat);
			$titleCat = $dom->createTextNode($titleCatText);
			$bibl->appendChild($titleCat);
		} 

		// Add a note to the bibl element
		if($item->comment) {
			//Avoid &amp;amp;
			$text = html_entity_decode($item->comment);
			$commentText = $dom->createTextNode($text);
			$comment = $dom->createElement('note');
			$comment->appendChild($commentText);
			$bibl->appendChild($comment);
		}
		//Insert a pagebreak in the middle of listBibl
		if(isset($pageBreak)) {
			$listBibl->appendChild($pageBreak);
		}		
		$listBibl->appendChild($bibl);

		unset($bibl);
		$count++;
		$lastHistSubject = $item->histSubject;
		$lastPageCat = $item->pageCat;
		}
	}

function insertBibliography($dom, $data, $catalogue) {

	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$listBibl = $dom->createElement('listBibl');
	$listBibl->setAttribute('type', 'bibliography');

	$count = 0;	

	foreach($data as $item) {
		$bibl = $dom->createElement('bibl');

		$id = assignID($item->id, $count, $catalogue->fileName);		
		$bibl->setAttribute('xml:id', $id.'-reference');
		$bibl->setAttribute('corresp', $id);
		$bibl = insertBibliographicData($bibl, $dom, $item);
		
		$listBibl->appendChild($bibl);
		$count++;
	}
	
	$body->appendChild($listBibl);
	
}

function insertBibliographicData($bibl, $dom, $item) {
	if($item->titleBib) {
		//Avoid &amp;amp;
		$titleBibText = $dom->createTextNode(html_entity_decode($item->titleBib));
		$titleBib = $dom->createElement('title');
		$titleBib->appendChild($titleBibText);
		$bibl->appendChild($titleBib);
	}
	foreach($item->persons as $person) {

		$tagName = translateRoleTEI($person->role);
		if($tagName != 'author' and $tagName != 'editor') {
			$tagName = 'author';		
		}

		$persName = $dom->createTextNode($person->persName);
		$personElement = $dom->createElement($tagName);

		if($person->gnd) {
			$rs = $dom->createElement('rs');
			$rs->setAttribute('type', 'person');
			$rs->setAttribute('key', 'gnd_'.$person->gnd);
			$rs->appendChild($persName);
			$personElement->appendChild($rs);
		}
		else {
			$personElement->appendChild($persName);
		}

		$bibl->appendChild($personElement);
				
	}
	if($item->volumes > 1) {
		$extent = $dom->createElement('extent');
		$extentText = $dom->createTextNode($item->volumes.' Bde.');
		$extent->appendChild($extentText);
		$bibl->appendChild($extent);
	}
	foreach($item->places as $place) {
		$placeName = $dom->createTextNode($place->placeName);
		$pubPlace = $dom->createElement('pubPlace');
		$key = '';		
		if($place->geoNames) {
			$key = 'geoNames_'.$place->geoNames;		
		}
		elseif($place->getty) {
			$key = 'getty_'.$place->getty;
		}
		elseif($place->gnd) {
			$key = 'gnd_'.$place->gnd;
		}
		if($key != '') {
			$rs = $dom->createElement('rs');
			$rs->setAttribute('type', 'place');
			$rs->setAttribute('key', $key);
			$rs->appendChild($placeName);
			$pubPlace->appendChild($rs);
		}
		else {
			$pubPlace->appendChild($placeName);			
		}
		$bibl->appendChild($pubPlace);
	}
	if($item->publisher) {
		$publisherText = $dom->createTextNode(html_entity_decode($item->publisher));
		$publisher = $dom->createElement('publisher');
		$publisher->appendChild($publisherText);
		$bibl->appendChild($publisher);	
	}
	if($item->year) {
		$yearText = $dom->createTextNode($item->year);
		$year = $dom->createElement('date');
		$year->appendChild($yearText);
		$when = normalizeYear($item->year);
		if(preg_match('~[12][0-9]{3}~', $when) == TRUE) {
			$year->setAttribute('when', $when);
		}
		$bibl->appendChild($year);
	}
	if($item->manifestation['systemManifestation'] and $item->manifestation['idManifestation']) {
		$idnoText = $dom->createTextNode($item->manifestation['idManifestation']);	
		$idno = $dom->createElement('idno');
		$idno->appendChild($idnoText);
		$idno->setAttribute('type', $item->manifestation['systemManifestation']);
		$bibl->appendChild($idno);
	}
	return($bibl);
}

?>
