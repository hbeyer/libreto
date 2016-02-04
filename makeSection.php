<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('encode.php');
include('makeIndex.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeGeoDataSheet.php');
include('storeBeacon.php');
include('setConfiguration.php');

$thisCatalogue = setConfiguration('rehl');
$facets = $thisCatalogue->facets;
$folderName = fileNameTrans($thisCatalogue->heading);
$dataString = file_get_contents($folderName.'/data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

$test = makeSections($data, 'numberCat');
foreach($test as $test) {
	//makeVolumes($test);
}
die;

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

//This function deletes items with $itemInVolume other than 0 from a section and replaces them with an object of the class volume, which contains these items as $content
function makeVolumes($section) {
	$count = 0;
	foreach($section->content as $item) {
		$number = $item->numberCat;
		$position = $item->itemInVolume;
		if($position > 0) {
			$count2 = 0;
			$volume = new volume();
			foreach($section->content as $item2) {
				if($item2->numberCat == $number) {
					if($item2->itemInVolume == 1) {
						$rememberPosition1 = $count2++;
					}
				$volume->content[$item2->itemInVolume] = $item;
				unset($section->content[$count2]);
				}
				$count2++;
			}
			if($rememberPosition1) {
				$section->content[$rememberPosition1] = $volume;
			}
			else {
				$section->content[] = $volume;
			}
		var_dump($volume);
		}
		$count++;
	}
}	

// This function converts an array of objects of the class section into a list in HTML format. The variable $thisCatalogue contains an object of the type catalogue and supplies information on the fileName ($thisCatalogue->key) and the URL base of the digitized version ($thisCatalogue->base). The function displays content either as text, for monographic entries, or as unordered list, for miscellanies.
	
function makeList($structuredData, $thisCatalogue) {	
	$folderName = fileNameTrans($thisCatalogue->heading);
	$count = 1;
	$content = '';
	foreach($structuredData as $section) {
		$info = '';
		if($section->authority['system'] == 'gnd') {
			$info = makeCollapseBeacon($section->authority['id'], $folderName, $thisCatalogue->key);
		}
		$content .= makeHeadline($section->level, $section->label, $info);
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
					<li>'.makeEntry($itemInVol, $catalogue, $count).'
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
function makeHeadline($level, $text, $info) {
	$result = '';
	if($level == 2) {
		$result = '<h3 id="'.translateAnchor($text).'">'.$text.$info.'</h3>';
	}
	elseif($level == 1) {
		$result = '<h3 id="'.translateAnchor($text).'">'.$text.$info.'</h3>';
	}
	return($result);
}

	
?>