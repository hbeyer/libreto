<?php

// The following functions serve to convert an array of objects of the type indexEntry into an array of objects of the type section. The function to select depends on the facet chosen. For the facets cat, persons and year there are special functions. All other facets are covered by the function makeSections.

function makeSectionsCat($data) {
	usort($data, 'compareCatalogue');
	$structuredData = array();
	$collectData = array();
	$lastLabel = $data[0]->histSubject;
	foreach($data as $item) {
		$collectData[] = $item;
		if($item->histSubject != $lastLabel) {
			$section = new section();
			$section->label = $lastLabel;
			$section->content = $collectData;
			$structuredData[] = $section;			
			$lastLabel = $item->histSubject;
			$collectData = array();
		}	
	}
		if(isset($collectData[0])) {
			$collectData[] = $item;
			$section = new section();
			$section->label = $lastLabel;
			$section->content = $collectData;
			$structuredData[] = $section;
		}
	return($structuredData);	
}

function makeSectionsAuthor($data) {
	$index = makeIndex($data, 'persons');
	$structuredData = array();
	$lastLetter = '0';
	foreach($index as $entry) {
		$letter = strtoupper(substr($entry->label, 0, 1));
		if($letter != $lastLetter) {
			$section = new section();
			$section->label = $letter;
			$lastLetter = $letter;
			$structuredData[] = $section;
		}
		$section = new section();
		$section->label = $entry->label;
		$section->level = 2;
		$section->authority = $entry->authority;
		foreach($entry->content as $itemID) {
			$section->content[] = $data[$itemID];	
		}
		$structuredData[] = $section;
	}
	return($structuredData);
}

// This is an auxiliary function for makeSectionsYear
function makeDecadeFromTo($year) {
	$decadeStart = $year - ($year % 10);
	$decadeEnd = $decadeStart + 10;
	$fromTo = $decadeStart.'–'.$decadeEnd;
	return($fromTo);
}	

function makeSectionsYear($data) {
	$index = makeIndex($data, 'year');
	$decades = array();
	$structuredData = array();
	$collectWithoutYear = array();
	$currentDecade = '';
	foreach($index as $entry) {
		if($entry->label == 'leer') {
			$collectWithoutYear[] = $entry;
		}
		elseif(is_numeric($entry->label)) {
			$year = $entry->label;
			$decade = makeDecadeFromTo($year);
			if($decade != $currentDecade) {
				$section = new section();
				$section->label = $decade;
				$structuredData[] = $section;
				$currentDecade = $decade;
			}
			$section = new section();
			$section->label = $year;
			$section->level = 2;
			foreach($entry->content as $keyBook) {
				$section->content[] = $data[$keyBook];
			}
			$structuredData[] = $section;
			unset($section);
		}
	}
		if(isset($collectWithoutYear[0])) {
			$section = new section();
			$section->label = 'ohne Jahr';
			foreach($collectWithoutYear as $entryWithoutYear) {
				foreach($entryWithoutYear->content as $keyBook) {
					$section->content[] = $data[$keyBook];
				} 
			}
			$structuredData[] = $section;
		}
		return($structuredData);
}

function makeSections($data, $field) {
	$index = makeIndex($data, $field);
	$structuredData = array();
	foreach($index as $entry) {
		$section = new section();
		if($entry->label == 'leer') {
			$section->label = 'ohne Kategorie';
		}
		else {
			$section->label = $entry->label;
		}
		foreach($entry->content as $keyBook) {
			$section->content[] = $data[$keyBook];
		}
		$structuredData[] = $section;
	}
	return($structuredData);	
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
