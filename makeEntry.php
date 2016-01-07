<?php

function makeAuthors($personList) {
	$result = '';
	$separator = '</span>/<span class="authorName">';
	$list = array();
	if(isset($personList[0])) {
		foreach($personList as $person) {
			//if($person->role == 'author') {
				$list[$person->gnd] = $person->name;
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
			$list[] = $place->name;
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
		$result = 'Eintrag im Altkatalog:<span class="titleOriginal-single"> '.$titleOriginal.'</span> <a href="'.$base.$imageCat.'" title="Titel im Altkatalog" target="_blank">S. '.$pageCat.', Nr. '.$numberCat.'</a><br/>';
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
	
function makeProof($system, $id) {
	include('targetData.php');
	$result = '';
	$translate = array('Ä' => 'ae', 'Ö' => 'oe', 'Ü' => 'ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss');
	$systemClean = strtr($system, $translate);
	$systemClean = strtolower(str_replace(' ', '', $systemClean));
	$hay = strtolower('#'.$system.$id);
	$test = strrpos($hay, 'bestimm');
	if($test != 0 or ($system == '' and $id == '')) {
		$result = 'Ausgabe nicht bestimmbar<br/>';
	}
	elseif(array_key_exists($systemClean, $bases)) {
		$ending = '';
		if(array_key_exists($systemClean, $endings)) {
			$ending = $endings[$systemClean];
		}
		if($systemClean == 'parisbnf' and substr($id, 5, 0 == 'FRBNF')) {
			$id = substr($id, 5);			
		}
		if($systemClean == 'buva') {
			$id = str_pad($id, 9, '0');			
		}
		$result = '<span class="heading_info">Nachweis: </span><a href="'.$bases[$systemClean].$id.$ending.'" target="_blank">'.$system.'</a><br/>';
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
	$result = '';
	if($text != '') {
		//preg_replace('~(VD1[67]\s[A-Z0-9 :]+)~', '~<a href="http://gateway-bayern.de/$0">$0</a>~', $text);
		$result = '<span class="comment">'.$text.'</span>';
	}
	return($result);
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
		$result = '<span class="titleBib">[Titel der Vorlage:] '.$titleCat.'</span><span class="titleOriginal" style="display:none">'.$titleCat.'</span>';
	}
	return($result);
}	

function makeEntry($thisBook, $thisCatalogue, $id) {
	$buffer = makeAuthors($thisBook->persons).makeTitle($thisBook->titleBib, $thisBook->titleCat).makePublished(makePlaces($thisBook->places), $thisBook->publisher, $thisBook->year).' <a id="linkid'.$id.'" href="javascript:toggle(\'id'.$id.'\')">Mehr</a>
				<div id="id'.$id.'" style="display:none; padding-top:0px; padding-bottom:15px; padding-left:10px;">'.makeSourceLink($thisBook->titleCat, $thisCatalogue->base, $thisBook->imageCat, $thisBook->pageCat, $thisBook->numberCat).makeDigiLink($thisBook->digitalCopy).makeProof($thisBook->manifestation['system'], $thisBook->manifestation['id']).makeComment($thisBook->comment).'</div>';
	return($buffer);
}
	
?>	