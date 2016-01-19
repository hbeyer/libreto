<?php

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
		$result = '<span class="authorName">'.implode($separator, $list).': </span>';
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
	
function makeSourceLink($titleOriginal, $base, $imageCat, $pageCat, $numberCat)	{
	$result = '';
	if($imageCat != '') {
		$result = 'Titel im Altkatalog:<span class="titleOriginal-single"> '.$titleOriginal.'</span> <a href="'.$base.$imageCat.'" title="Titel im Altkatalog" target="_blank">S. '.$pageCat.', Nr. '.$numberCat.'</a><br/>';
	}
	return($result);
}	
	
function makeDigiLink($digi) {
	$result = '';
	$resolver = '';
	if($digi != '') {
		$split = strrpos($digi, 'urn:');
		// Hier muss der Fall einkalkuliert werden, dass nur der URN vorhanden ist. S. Z. 82
		if($split > 0) {
			$digi = substr($digi, $split);
			$resolver = 'http://nbn-resolving.de/';
		}
		$result = '<span class="heading_info">Digitalisat: </span><a href="'.$resolver.$digi.'" target="_blank">'.$digi.'</a><br />';
	}
	return($result);
}
	
function makeProof($thisBook) {
	include('targetData.php');
	$system = $thisBook->manifestation['system'];
	$id = $thisBook->manifestation['id'];
	$level = $thisBook->bibliographicalLevel;
	$result = '';
	$systemClean = translateAnchor($system);
	$systemClean = strtolower(str_replace(' ', '', $systemClean));
	$hay = strtolower('#'.$system.$id);
	if(strrpos($hay, 'bestimm') != 0 or ($system == '' and $id == '')) {
		$result = 'Ausgabe nicht bestimmbar<br/>';
	}
	elseif(strrpos($hay, 'nach') != 0 or $level == 'noEvidence') {
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

function insertLink($text, $pattern, $target) {
	$targetArray = explode('{ID}', $target, 2);
	$base = $targetArray[0];
	$end = $targetArray[1];
	$replacement = '<a href="'.$base.'$1'.$end.'" target="_blank">$0</a>';
	$text = preg_replace($pattern, $replacement, $text);
	return($text);
}

// Diese Funktion soll alternative reguläre Ausdrücke wie ~PPN ([0-9X]{9})|GBV ([0-9X]{9})~ verarbeiten, ist aber schwierig.
function insertLinkNew($text, $pattern, $target) {
	$id = '';
	$targetArray = explode('{ID}', $target, 2);
	$base = $targetArray[0];
	$end = $targetArray[1];
	preg_match_all($pattern, $text, $matches);
	if($matches[0][0]) {
		$linkText = $matches[0][0];
		$id = $matches[1][0];
		if($id == '' and isset($matches[2][0])) {
			$id = $matches[2][0];
		}
	}
	echo '<p>'.$linkText.' '.$id.'</p>';
}


function makeTitle($titleBib, $titleCat) {
	$result = '';
	if($titleBib AND $titleCat) {
		$result = '<span class="titleBib">'.$titleBib.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}
	elseif($titleBib) {
		$result = '<span class="titleBib">'.$titleBib.'</span><span class="titleOriginal" style="display:none">[Recherchierter Titel:] '.$titleBib.'</span>';		
	}
	elseif($titleCat) {
		$result = '<span class="titleBib">[Titel im Altkatalog:] '.$titleCat.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}
	return($result);
}	

function makeEntry($thisBook, $thisCatalogue, $id) {
	$buffer = makeAuthors($thisBook->persons).makeTitle($thisBook->titleBib, $thisBook->titleCat).makePublished(makePlaces($thisBook->places), $thisBook->publisher, $thisBook->year).' <a id="linkid'.$id.'" href="javascript:toggle(\'id'.$id.'\')">Mehr</a>
				<div id="id'.$id.'" style="display:none; padding-top:0px; padding-bottom:15px; padding-left:10px;">'.makeSourceLink($thisBook->titleCat, $thisCatalogue->base, $thisBook->imageCat, $thisBook->pageCat, $thisBook->numberCat).makeDigiLink($thisBook->digitalCopy).makeProof($thisBook).makeComment($thisBook->comment).'</div>';
	return($buffer);
}
	
?>	