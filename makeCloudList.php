<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('sort.php');
include('encode.php');
include('languageCodes.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');

include('makeGeoDataSheet.php');
include('storeBeacon.php');
include('setConfiguration.php');

$thisCatalogue = setConfiguration('bahn');
$folderName = fileNameTrans($thisCatalogue->heading);

$dataString = file_get_contents($folderName.'/data-'.$thisCatalogue->key);
$data = unserialize($dataString);
unset($dataString);

makeCloudFile($data, 'persName', 50);

function makeCloudFile($data, $field, $limit) {
	if($field == 'persName') {
		$cloudArrays = makeCloudArraysPersons($data);
	}
	else {
		$cloudArrays = makeCloudArrays($data, $field);
	}
	$weightArray = $cloudArrays['weightArray'];
	$size = count($weightArray);
	if($limit <= $size) {
		$weightArray = shortenWeightArray($cloudArrays['weightArray']);
	}
	$cloudContent = fillCloudList($weightArray, $cloudArrays['nameArray'], $limit);
	saveCloudList($cloudContent);
}

function makeCloudArrays($data, $field) {
	$index = makeIndex($data, $field);
	$count = 0;
	foreach($index as $entry) {
		if($entry->label != 'ohne Kategorie') {
			$text = htmlspecialchars($entry->label);
			$text = preprocessText($text, $field);
			$weight = count($entry->content);
			$weightArray[$count] = $weight;
			$nameArray[$count] = $text;
			$count ++;
		}
	}
	arsort($weightArray);
	$return = array('weightArray' => $weightArray, 'nameArray' => $nameArray);
	return($return);
}

function makeCloudArraysPersons($data) {
	$index = makeIndex($data, 'persName');
	$count = 0;
	foreach($index as $entry) {
		$id = $count;
		if($entry->authority['system'] == 'gnd') {
			$id = $entry->authority['id'];
		}
		$name = prependForename($entry->label);
		$weight = count($entry->content);
		$weightArray[$id] = $weight;
		$nameArray[$id] = $name;
		$count ++;
	}
	arsort($weightArray);
	$return = array('weightArray' => $weightArray, 'nameArray' => $nameArray);
	return($return);
}

function shortenWeightArray($weightArray) {
	$lastWeight = array_pop($weightArray);
	$currentWeight = $lastWeight;
	while($lastWeight == $currentWeight) {
		$currentWeight = array_pop($weightArray);
	}
	return($weightArray);
}

function fillCloudList($weightArray, $nameArray, $limit) {
	$count = 0;
	foreach($weightArray as $id => $weight) {
		//$name = htmlspecialchars($nameArray[$id]);
		$name = $nameArray[$id];
		$row = array('text' => $name, 'weight' => $weight);
		if(preg_match('~^[0-9X]{8,10}$~', $id)) {
			$link = 'http://d-nb.info/gnd/'.$id;
			$row['link'] = $link;
		}
		$content[] = $row;
		$count++;
		if($count > $limit) {
			break;
		}
	}
	return($content);
}

function preprocessText($text, $field) {
	if($field == 'titleBib') {
		$text = replaceArrowBrackets($text);
		$shortText = substr($text, 0, 30);
		if(strlen($text) > 30) {
			print strlen($text)."\n";
			$shortText .= '...';
		}
		$text = $shortText;
	}
	return($text);
}

function saveCloudList($content) {
	$file = fopen('jqcloud/cloudList.json', 'w');
	fwrite($file, json_encode($content), 30000000);
	fclose($file);
}

function prependForename($name) {
	$parts = explode(', ', $name);
	if(isset($parts[1]) == TRUE and isset($parts[2]) == FALSE) {
		$name = $parts[1].' '.$parts[0];
	}
	return($name);
}

?>