<?php

// The following functions serve to convert an array of objects of the type indexEntry into an array of objects of the type section. The function to select depends on the facet chosen. For the facets cat, persons and year there are special functions. All other facets are covered by the function makeSections.

function makeSections($data, $field) {
	$index = makeIndex($data, $field);
	$structuredData = array();
	foreach($index as $entry) {
		$section = new section();
		$section->label = $entry->label;
		$section->level = $entry->level;
		$section->authority = $entry->authority;
		$section->geoData = $entry->geoData;
		foreach($entry->content as $idItem) {
			$section->content[] = $data[$idItem];
		}
		$structuredData[] = $section;
	}
	$structuredData = addHigherLevel($structuredData, $field);
	return($structuredData);
}

function addHigherLevel($structuredData, $field) {
	$newStructure = array();
	$previousSection = new section();
	foreach($structuredData as $section) {
		$higherSection = makeHigherSection($section, $previousSection, $field);
		if(is_object($higherSection) == TRUE) {
			$newStructure[] = $higherSection;
		}
		$newStructure[] = $section;
		$previousSection = $section;
	}
	return($newStructure);
}

function makeHigherSection($section, $previousSection, $field) {
	$higherSection = '';
	if($field == 'persName') {
		$previousLetter = substr($previousSection->label, 0, 1);
		$currentLetter = substr($section->label, 0, 1);
		if($previousLetter != $currentLetter) {
			$higherSection = new section();
			$higherSection->label = $currentLetter;
		}
	}
	elseif($field == 'year') {
		if($section->label != 'ohne Jahr') {
			$previousDecade = makeDecadeFromTo($previousSection->label);
			$currentDecade = makeDecadeFromTo($section->label);
			if($previousDecade != $currentDecade) {
				$higherSection = new section();
				$higherSection->label = $currentDecade;
			}
		}
	}
	return($higherSection);
}

// This is an auxiliary function for makeHigherSection
function makeDecadeFromTo($year) {
	$decadeStart = $year - ($year % 10);
	$decadeEnd = $decadeStart + 10;
	$fromTo = $decadeStart.'–'.$decadeEnd;
	return($fromTo);
}	

// This function converts an array of objects of the class section into a list in HTML format. The variable $thisCatalogue contains an object of the type catalogue and supplies information on the fileName ($thisCatalogue->key) and the URL base of the digitized version ($thisCatalogue->base).
	
function makeList($structuredData, $thisCatalogue) {	
	$folderName = fileNameTrans($thisCatalogue->heading);
	$count = 1;
	$content = '';
	foreach($structuredData as $section) {
		$info = '';
		$levelHeading = '2';
		if($section->level == 2) {
			$levelHeading = '3';
		}
		if($section->authority['system'] == 'gnd') {
			$info = makeCollapseBeacon($section->authority['id'], $folderName, $thisCatalogue->key);
		}
		if($section->label) {
			$headline = $section->label;
			$content .= '<h'.$levelHeading.' id="'.translateAnchor($headline).'">'.$headline.$info.'</h'.$levelHeading.'>';
		}
		else {
			$headline = 'Ohne Kategorie';
			$content .= '<h'.$levelHeading.' id="ohneKategorie">'.$headline.$info.'</h'.$levelHeading.'>';
		}
		foreach($section->content as $thisBook) {
			$content .= '
			<div class="entry">'.makeEntry($thisBook, $thisCatalogue, $count).'
			</div>';
			$count++;
		}
	}
	return($content);	
}	

// The function produces a link to further information on persons. It is called by the function makeList, if GND data is submitted in $section->authority. To work, it needs serialized BEACON data in a file named beaconStore-{catalogue key}. Therefore you have to run the function storeBeacon previously.
	
function makeCollapseBeacon($gnd, $folderName, $thisCatalogue) {
	$beaconString = file_get_contents($folderName.'/beaconStore-'.$thisCatalogue);
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
	
function makeBeaconLink($gnd, $target) {
	$translate = array('{ID}' => $gnd);
	$link = strtr($target, $translate);
	return($link);
}

	
?>
