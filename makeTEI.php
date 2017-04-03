<?php

/*
Ausstehend in diesem Skript:
Regelung für die Werkebene bzw. für nicht identifizierte Titel
Klammern von Sammelbänden
*/

function makeTEI($data, $folder, catalogue $catalogue) {
	//Make sure that every item has an ID
	$count = 0;
	foreach($data as $item) {
		$item->id = assignID($item->id, $count, $catalogue->fileName);
		$count++;
	}
	
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$dom->load('templateTEI.xml');
	insertMetadata($dom, $catalogue);
	insertTranscription($dom, $data, $catalogue);
	insertPageBreaks($dom, $data);
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

function insertTranscription($dom, $data, catalogue $catalogue) {
	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$lastPageCat = '';
	
	$index = makeIndex($data, 'histSubject');
	$structuredData = array();
	foreach($index as $entry) {
		$section = new section();
		$section->label = $entry->label;
		foreach($entry->content as $idItem) {
			$section->content[] = $data[$idItem];
		}
		$section = joinVolumes($section);
		$structuredData[] = $section;
	}
	
	foreach($structuredData as $section) {
		$listBibl = $dom->createElement('listBibl');
		$listBibl->setAttribute('type', 'transcription');
		$textHead = $dom->createTextNode($section->label);
		$head = $dom->createElement('head');
		$head->appendChild($textHead);	
		$listBibl->appendChild($head);
		foreach($section->content as $object) {
			if(get_class($object) == 'volume') {
				insertVolumeTrans($dom, $listBibl, $object);
			}			
			elseif(get_class($object) == 'item') {
				insertItemTrans($dom, $object, $listBibl);
			}
		}
		$body->appendChild($listBibl);
	}
}

function insertVolumeTrans($dom, $listBibl, $volume) {
	$div = $dom->createElement('div');
	$div->setAttribute('type', 'volume');
	foreach($volume->content as $item) {
		insertItemTrans($dom, $item, $div);
	}
	$listBibl->appendChild($div);
}

function insertItemTrans($dom, $item, $target) {
	// Insert a bibl element for each catalogue entry
	$bibl = $dom->createElement('bibl');
	if($item->numberCat) {
		$bibl->setAttribute('n', $item->numberCat);
	}
	$bibl->setAttribute('xml:id', $item->id);
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
	$target->appendChild($bibl);
}

function insertBibliography($dom, $data, $catalogue) {

	$bodyNodeList = $dom->getElementsByTagName('body');
	$body = $bodyNodeList->item(0);
	$listBibl = $dom->createElement('listBibl');
	$listBibl->setAttribute('type', 'bibliography');

	$count = 0;	

	foreach($data as $item) {
		$bibl = $dom->createElement('bibl');
		$bibl->setAttribute('xml:id', $item->id.'-reference');
		$bibl->setAttribute('corresp', $item->id);
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

function insertPageBreaks($dom, $data) {
	
	$firstItems = array();
	$lastPageCat = '';
	foreach($data as $item) {
		$pageCat = $item->pageCat;
		if($pageCat != $lastPageCat) {
			$firstItems[$item->id] = $pageCat;
		}
		$lastPageCat = $pageCat;
	}
	
	foreach($firstItems as $id => $pageNo) {
		$xp = new DOMXPath($dom);
		$expression = '//bibl[@xml:id="'.$id.'"]';
 		$biblNodes = $xp->evaluate($expression);
		$bibl = $biblNodes->item(0);
		$pb = $dom->createElement('pb');
		$pb->setAttribute('n', $pageNo);
		
		$expressionParent = '//bibl[@xml:id="'.$id.'"]/parent::*';
		$parentNodes = $xp->evaluate($expressionParent);
		$parent = $parentNodes->item(0);
		
		$expressionPreceding = '//bibl[@xml:id="'.$id.'"]/preceding-sibling::*';
		$precedingNodes = $xp->evaluate($expressionPreceding);
		$preceding = $precedingNodes->item(0);
		
		if($preceding->tagName == 'head') {
			$parent->insertBefore($pb, $preceding);
		}
		else {
			$parent->insertBefore($pb, $bibl);
		}	
	}
	
}

?>
