<?php

include('classDefinition.php');
include('makeEntry.php');
include('ingest.php');
include('sort.php');
include('encode.php');
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

$limit = 50;
$cloudArrays = makeCloudListPersons($data);
$weightArray = $cloudArrays['weightArray'];
$size = $weightArray;
if($limit >= $size) {
	$weightArray = shortenWeightArray($cloudArrays['weightArray']);
}
$cloudContent = fillCloudList($weightArray, $cloudArrays['nameArray'], $limit);
saveCloudList($cloudContent);


function makeCloudList($data, $field) {
	$index = makeIndex($data, $field);
	$count = 0;
	foreach($index as $entry) {
		$name = $entry->label;
		$weight = count($entry->content);
		$weightArray[$count] = $weight;
		$nameArray[$count] = $name;
		$count ++;
	}
	arsort($weightArray);
	$return = array('weightArray' => $weightArray, 'nameArray' => $nameArray);
	return($return);
}

function makeCloudListPersons($data) {
	$index = makeIndex($data, 'persName');
	$count = 0;
	foreach($index as $entry) {
		$id = $count;
		if($entry->authority['system'] == 'gnd') {
			$id = $entry->authority['id'];
		}
		$name = $entry->label;
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
		$row = array('text' => $nameArray[$id], 'weight' => $weight);
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

function saveCloudList($content) {
	$file = fopen('jqcloud/cloudList.json', 'w');
	fwrite($file, json_encode($content), 30000000);
	fclose($file);
}

?>