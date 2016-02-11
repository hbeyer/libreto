<?php

function getTextContent($nodeList) {
	$resultArray = array();
	foreach($nodeList as $node) {
		$resultArray[] = $node->textContent;
	}
	if(isset($resultArray[0]) and isset($resultArray[1])) {
		return(implode('|', $resultArray));
	}
	if(isset($resultArray[0])) {
		return($resultArray[0]);
	}
}

function getAttributeFromNodeList($nodeList, $attribute) {
	$resultArray = array();
	foreach($nodeList as $node) {
		$resultArray[] = $node->getAttribute($attribute);
	}
	if(isset($resultArray[0]) and isset($resultArray[1])) {
		return(implode('|', $resultArray));
	}
	if(isset($resultArray[0])) {
		return($resultArray[0]);
	}
}

?>