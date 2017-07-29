<?php

function makeEntry($item, $base) {
	$persons = makePersons($item->persons);
	$published = makePublicationString($item);
	$originalLink = makeOriginalLink($item->originalItem);
	$sourceLink = makeSourceLink($item, $base);
	$workLink = makeWorkLink($item->work);
	$digiLink = makeDigiLink($item->digitalCopy);
	$proof = makeProof($item);
	$copiesHAB = makeCopiesHAB($item->copiesHAB);
	$comment = makeComment($item->comment);
	ob_start();
	include 'entry.phtml';
	$return = ob_get_contents();
	ob_end_clean();
	return($return);
}

function makePersons($persons) {
	if($persons == array()) {
		return;
	}
	$persArray = array();
	$gnds = array();
	foreach($persons as $person) {
		if($person->gnd and in_array($person->gnd, $gnds) == FALSE) {
			$persArray[] = $person->persName;
			$gnds[] = $person->gnd;
		}
	}
	$result = implode('</span>/<span class="authorName">', $persArray);
	$result = '<span class="authorName">'.$result.'</span>';
	return($result);
}

function makePublicationString($item) {
	$result = '';
	$placeString = '';
	if (isset($item->places[0])) {
		$placeArray = array();
		foreach ($item->places as $place) {
			$placeArray[] = $place->placeName;
		}
		$result .= $placeString.': ';
	}
	$publisher = $item->publisher;
	$date = $item->year;
	$sep1 = '';
	$sep2 = '';
	if ($placeString and $publisher) {
		$sep1 = ': ';
	}
	if ($publisher and $date) {
		$sep2 = ', ';
	}
	elseif ($placeString and $date) {
		$sep1 = ' ';
	}
	$result = $placeString.$sep1.$publisher.$sep2.$date;
	return($result);
}

function makeOriginalLink($originalItem) {
	$result = '';
	$institutionOriginal = $originalItem['institutionOriginal'];
	$shelfmarkOriginal = $originalItem['shelfmarkOriginal'];
	$targetOPAC = $originalItem['targetOPAC'];
	$searchID = $originalItem['searchID'];
	$provenanceAttribute = $originalItem['provenanceAttribute'];
	$digitalCopyOriginal = $originalItem['digitalCopyOriginal'];
	
	if($institutionOriginal and $shelfmarkOriginal and $targetOPAC == '') {
		$result = 'Originalexemplar: '.$institutionOriginal.', '.$shelfmarkOriginal;
	}
	elseif($institutionOriginal and $shelfmarkOriginal and $targetOPAC and $searchID == '') {
		$link = makeBeaconLink($shelfmarkOriginal, $targetOPAC);
		$result = 'Originalexemplar: <a href="'.$link.'">'.$institutionOriginal.', '.$shelfmarkOriginal.'</a>';
	}
	elseif($institutionOriginal and $shelfmarkOriginal and $targetOPAC and $searchID) {
		$link = makeBeaconLink($searchID, $targetOPAC);
		$result = 'Originalexemplar: <a href="'.$link.'">'.$institutionOriginal.', '.$shelfmarkOriginal.'</a>';
	}
	if($result and $provenanceAttribute) {
		$result .= '; Grund für Zuschreibung: '.$provenanceAttribute;
	}
	if($result and $digitalCopyOriginal) {
		$result .= '; Digitalisat: '.makeDigiLink($digitalCopyOriginal);
	}
	return($result);
}	

function makeSourceLink($item, $base) {
	$result = '';
	$link = '';
	if($base and $item->imageCat) {
		$link = ' <a href="'.$base.$item->imageCat.'" title="Titel im Altkatalog" target="_blank">S. '.$item->pageCat.', Nr. '.$item->numberCat.'</a>';
	}
	if($item->titleCat) {
		$result = '<span class="titleOriginal-single">Titel im Altkatalog: <i>'.$item->titleCat.'</i></span>'.$link;
	}
	return($result);
}

function makeWorkLink($work) {
	if($work['systemWork'] and $work['idWork']) {
	include('targetData.php');
	$systemClean = translateAnchor($work['systemWork']);
	$systemClean = trim($work['systemWork']);
	$systemClean = strtolower($systemClean);
	$target = $basesWorks($systemClean);
	$link = makeBeaconLink($work['idWork'], $target);
	$result = 'Werk: <a href="'.$link.'" title="Datensatz zum Werk aufrufen" target="_blank">'.$work['systemWork'].' '.$work['idWork'].'</a>';
	}
}
	
function makeDigiLink($digi) {
	$title = 'Digitalisat';
	$result = '';
	$resolver = '';
	if($digi != '') {
		if(substr($digi, 0, 4) == 'KEYP') {
			$title = 'Schl&uuml;sselseiten';
			$digi = substr($digi, 4);
		}
		$urn = strstr($digi, 'urn:');
		if($urn != FALSE) {
			$digi = $urn;
			$resolver = 'http://nbn-resolving.de/';
		}
		$result = '<span class="heading_info">'.$title.': </span><a href="'.$resolver.$digi.'" target="_blank">'.$digi.'</a>';
	}
	return($result);
}
	
function makeProof($item) {
	include('targetData.php');
	$system = '';
	if(isset($item->manifestation['systemManifestation'])) {
		$system = $item->manifestation['systemManifestation'];
	}
	$id = '';
	if(isset($item->manifestation['idManifestation'])) {
		$id = $item->manifestation['idManifestation'];
	}
	$result = '';
	$systemClean = translateAnchor($system);
	$systemClean = strtolower(str_replace(' ', '', $systemClean));
	$hay = strtolower('#'.$system.$id);
	if(strrpos($hay, 'bestimm') != 0 or ($system == '' and $id == '')) {
		$result = 'Ausgabe nicht bestimmbar';
	}
	elseif(strrpos($hay, 'nach') != 0) {
		$result = 'Ausgabe nicht nachgewiesen';
		}
	elseif(array_key_exists($systemClean, $bases)) {
		if($systemClean == 'parisbnf' and substr($id, 5, 0 == 'FRBNF')) {
			$id = substr($id, 5);
		}
		if($systemClean == 'buva') {
			$id = str_pad($id, 9, '0');			
		}
		$translateID = array('{ID}' => $id);
		$link = strtr($bases[$systemClean], $translateID);
		$result = '<span class="heading_info">Nachweis: </span><a href="'.$link.'" target="_blank">'.$system.'</a>';
	}
	else {
		$page = '';
		if($id != '') {
			$page = ', '.$id;
		}
		$result = '<span class="heading_info">Nachweis: </span>'.$system.$page;
	}
	return($result);
}

function makeCopiesHAB($copies) {
	$base = 'http://opac.lbs-braunschweig.gbv.de/DB=2/SET=31/TTL=1/CMD?ACT=SRCHA&TRM=sgb+';
	$links = array();
	$translation = array('(' => '', ')' => '');
	foreach($copies as $copy) {
		$copyOPAC = strtr($copy, $translation);
		$links[] = '<a href="'.$base.urlencode($copyOPAC).'" target="_blank">'.$copy.'</a>';
	}
	$result = implode('; ', $links);
	$result = 'Exemplare der HAB: '.$result;
	return($result);
}

function makeComment($text) {
	include('targetData.php');
	$result = '';
	if($text != '') {
		foreach($patternSystems as $key => $pattern) {
			$target = $bases[$key];
			$text = insertLink($text, $pattern, $target);
			}
		$result = '<span class="comment">'.$text.'</span>';
	}
	return($result);
}

// The function automatically replaces the identifiers the patterns of which are listed in targetData.php by links to the respective database
function insertLink($text, $pattern, $target) {
	$targetArray = explode('{ID}', $target, 2);
	$base = $targetArray[0];
	$end = $targetArray[1];
	$replacement = '<a href="'.$base.'$1'.$end.'" target="_blank">$0</a>';
	$text = preg_replace($pattern, $replacement, $text);
	return($text);
}
	
?>	