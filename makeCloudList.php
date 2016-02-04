﻿<?php

function makeCloudFile($data, $field, $limit, $folder) {
	$path = '../'.$folder.'/'.$folder.'-'.$field.'.html#';
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
	$cloudContent = fillCloudList($weightArray, $cloudArrays['nameArray'], $limit, $path);
	saveCloudList($cloudContent, $field, $folder);
}

function makeCloudPageContent() {
	$content = '<div id="wordcloud"></div>';
	return($content);
}

function makeCloudArrays($data, $field) {
	$index = makeIndex($data, $field);
	$count = 0;
	$weightArray = array();
	$nameArray = array();
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

function fillCloudList($weightArray, $nameArray, $limit, $path) {
	$count = 0;
	$content = '';
	foreach($weightArray as $id => $weight) {
		$name = $nameArray[$id];
		$row = array('text' => $name, 'weight' => $weight);
		if(preg_match('~^[0-9X]{8,10}$~', $id)) {
			$link = $path.$id;
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

function saveCloudList($content, $field, $folder) {
	$file = fopen($folder.'/cloudList-'.$field.'.json', 'w');
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