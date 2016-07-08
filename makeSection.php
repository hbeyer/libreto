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
		$previousLetter = strtoupper(substr($previousSection->label, 0, 1));
		$currentLetter = strtoupper(substr($section->label, 0, 1));
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

//This function replaces the content of the section-object, thereby putting item-objects with $itemInVolume > 0 into volume-objects

function joinVolumes($section) {
	$newContent = array();
	$buffer = array();
	$lastNumberCat = '';
	foreach($section->content as $item) {
		if($item->itemInVolume == 0) {
			if(isset($buffer[0])) {
				$newContent[] = makeVolume($buffer);
				$buffer = array();
			}
			$newContent[] = $item;
		}
		elseif($item->itemInVolume > 0 and $item->numberCat != $lastNumberCat and isset($buffer[0])) {
			$newContent[] = makeVolume($buffer);
			$buffer = array();
			$buffer[] = $item;
		}
		elseif($item->itemInVolume > 0) {
			$buffer[] = $item;
		}
		$lastNumberCat = $item->numberCat;
	}
	if(isset($buffer[0])) {
		$newContent[] = makeVolume($buffer);
	}
	$section->content = $newContent;
	return($section);
}

function makeVolume($buffer) {
	uasort($buffer, 'compareItemInVolume');
	$result = new volume();
	$result->content = $buffer;
	return($result);
}

function compareItemInVolume($a, $b) {
	if($a->itemInVolume == $b->itemInVolume) {
		return 0;
	}
	else {
		return ($a->itemInVolume < $b->itemInVolume) ? -1 : 1;
	}
}

// This function converts an array of objects of the class section into a list in HTML format. The variable $thisCatalogue contains an object of the type catalogue and supplies information on the fileName ($thisCatalogue->key) and the URL base of the digitized version ($thisCatalogue->base). The function displays content either as text, for monographic entries, or as unordered list, for miscellanies.
	
function makeList($structuredData, $thisCatalogue, $folderName) {	
	$count = 1;
	$content = '';
	foreach($structuredData as $section) {
		$info = '';
		$anchor = '';
		if($section->authority['system'] == 'gnd') {
			$info = makeCollapseBeacon($section->authority['id'], $folderName);
			$anchor = 'person'.$section->authority['id'];
		}
		$content .= makeHeadline($section->level, $section->label, $info, $anchor);
		foreach($section->content as $item) {
			if(get_class($item) == 'item') {
					$content .= '
			<div class="entry">'.makeEntry($item, $thisCatalogue, $count).'
			</div>';
			}
			elseif(get_class($item) == 'volume') {
				$content .= '
			<div class="entry">Sammelband
				<ul>';
				foreach($item->content as $itemInVol) {
					$content .= '
					<li class="entry-list">'.makeEntry($itemInVol, $thisCatalogue, $count).'
					</li>';
					$count++;
				}
				$content .= '
				</ul>
			</div>';
			}
			$count++;
		}
	}
	return($content);	
}

// This is an auxiliary function for makeList, producing headlines or leaving them out (in case of facet id, 0 is assigned by function makeIndex)
function makeHeadline($level, $text, $info, $anchor) {
	$result = '';
	if($anchor == '') {
		$anchor = translateAnchor($text);
	}
	if($level == 2) {
		$result = '<h3 id="'.$anchor.'">'.$text.$info.'</h3>';
	}
	elseif($level == 1) {
		$result = '<h2 id="'.translateAnchor($text).'">'.$text.$info.'</h2>';
	}
	return($result);
}

	
?>