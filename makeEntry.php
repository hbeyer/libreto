<?php

function makeEntry($thisBook, $thisCatalogue, $id) {
	$buffer = makeAuthors($thisBook->persons).makeTitle($thisBook->titleBib, $thisBook->titleCat, $thisBook->work).makePublished(makePlaces($thisBook->places), $thisBook->publisher, $thisBook->year).' <a id="linkid'.$id.'" href="javascript:toggle(\'id'.$id.'\')">Mehr</a>
				<div id="id'.$id.'" style="display:none; padding-top:0px; padding-bottom:15px; padding-left:10px;">'.makeSourceLink($thisBook, $thisCatalogue->base).makeOriginalLink($thisBook->originalItem).makeWorkLink($thisBook->work).makeDigiLink($thisBook->digitalCopy).makeProof($thisBook).makeComment($thisBook->comment).'</div>';
	return($buffer);
}

function makeAuthors($personList) {
	$result = '';
	$separator = '</span>/<span class="authorName">';
	$list = array();
	if(isset($personList[0])) {
		foreach($personList as $person) {
			//if($person->role == 'author') {
				$list[$person->gnd] = $person->persName;
				//}
		}
		$result = '<span class="authorList"><span class="authorName">'.implode($separator, $list).': </span></span>';
	}
	return($result);
}

function makePlaces($placeList) {
	$separator = '/';
	$list = array();
	foreach($placeList as $place) {
			$list[] = $place->placeName;
	}
	$result = implode($separator, $list);
	return($result);
}

function makePublished($places, $publisher, $year) {
	$result = '';	
	if($places and $publisher and $year) {
		$result .= ', '.$places.': '.$publisher.', '.$year;
	}
	elseif($places and $publisher) {
		$result .= ', '.$places.': '.$publisher;
	}
	elseif($publisher and $year) {
		$result .= ', '.$publisher.', '.$year;
	}
	elseif($places and $year) {
		$result .= ', '.$places.', '.$year;
	}
	elseif($places) {
		$result .= ', '.$places;
	}
	elseif($publisher) {
		$result .= ', '.$publisher;
	}
	elseif($year) {
		$result .= ', '.$year;
	}
	return('<span class="published">'.$result.'</span>');
}
	
function makeSourceLink($item, $base)	{
	$result = '';
	if($item->imageCat != '') {
		$result = 'Titel im Altkatalog:<span class="titleOriginal-single"> '.$item->titleCat.'</span> <a href="'.$base.$item->imageCat.'" title="Titel im Altkatalog" target="_blank">S. '.$item->pageCat.', Nr. '.$item->numberCat.'</a><br/>';
	}
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
	
	if($institutionOriginal and $shelfmarkOriginal) {
		$result = $institutionOriginal.', '.$shelfmarkOriginal;
		if($targetOPAC and $searchID) {
			$link = makeBeaconLink($searchID, $targetOPAC);
		}
		elseif($targetOPAC and $shelfmarkOriginal) {
			$link = makeBeaconLink($shelfmarkOriginal, $targetOPAC);
		}
		$result = '<a href="'.$link.'" target="_blank">'.$result.'</a>';		
		if($provenanceAttribute) {
			$result .= '; Grund für Zuschreibung: '.$provenanceAttribute;
		}
		if($digitalCopyOriginal) {
			$result .= '; Digitalisat: '.makeDigiLink($digitalCopyOriginal);
		}
		$result = 'Originalexemplar: '.$result.'<br/>';
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
	$result = '';
	$resolver = '';
	if($digi != '') {
		$urn = strstr($digi, 'urn:');
		if($urn != FALSE) {
			$digi = $urn;
			$resolver = 'http://nbn-resolving.de/';
		}
		$result = '<span class="heading_info">Digitalisat: </span><a href="'.$resolver.$digi.'" target="_blank">'.$digi.'</a><br />';
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
		$result = 'Ausgabe nicht bestimmbar<br/>';
	}
	elseif(strrpos($hay, 'nach') != 0) {
		$result = 'Ausgabe nicht nachgewiesen<br/>';
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
		$result = '<span class="heading_info">Nachweis: </span><a href="'.$link.'" target="_blank">'.$system.'</a><br/>';
	}
	else {
		$page = '';
		if($id != '') {
			$page = ', '.$id;
		}
		$result = '<span class="heading_info">Nachweis: </span>'.$system.$page.'<br/>';
	}
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


function makeTitle($titleBib, $titleCat, $work) {
	$titleWork = $work['titleWork'];
	$result = '';
	if($titleBib and $titleCat) {
		$result = '<span class="titleBib">'.$titleBib.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}
	elseif($titleBib) {
		$result = '<span class="titleBib">'.$titleBib.'</span><span class="titleOriginal" style="display:none">[Recherchierter Titel:] '.$titleBib.'</span>';
	}
	elseif($titleCat and $titleWork) {
		$result = '<span class="titleBib">[Titel des Werkes:] '.$titleWork.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}	
	elseif($titleCat) {
		$result = '<span class="titleBib">[Titel im Altkatalog:] '.$titleCat.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}
	return($result);
}

// The function produces a link to further information on persons. It is called by the function makeList, if GND data is submitted in $section->authority. To work, it needs serialized BEACON data in a file named beaconStore. Therefore you have to run the function storeBeacon previously.
	
function makeCollapseBeacon($gnd, $folderName) {
	$beaconString = file_get_contents($folderName.'/beaconStore');
	$beaconObject = unserialize($beaconString);
	unset($beaconString);
	$link = '';
	$linkData = array('<a href="http://d-nb.info/gnd/'.$gnd.'" title="Deutsche Nationalbibliothek" target="_blank">Deutsche Nationalbibliothek</a>');
	foreach($beaconObject->content as $beaconExtract) {
		if(in_array($gnd, $beaconExtract->content)) {
			$link = '<a href="'.makeBeaconLink($gnd, $beaconExtract->target).'" title="'.$beaconExtract->label.'" target="_blank">'.$beaconExtract->label.'</a>';
			$linkData[] = $link;
		}
	}
	$content = implode(' | ', $linkData);
	$collapse = '
		<a href="#'.$gnd.'" data-toggle="collapse"><span class="glyphicon glyphicon-info-sign" style="font-size:14px"></span></a>
		<div id="'.$gnd.'" class="collapse"><span style="font-size:14px">'.$content.'</span></div>';
	return($collapse);
}
	
?>	