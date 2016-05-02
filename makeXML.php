<?php

function saveXML($data, $catalogue, $folderName) {
	$dom = new DOMDocument('1.0', 'UTF-8');
	$dom->formatOutput = true;
	$rootElement = $dom->createElement('collection');
	$metadata = $dom->createElement('metadata');
	$heading = $dom->createElement('heading', $catalogue->heading);
	$year = $dom->createElement('year', $catalogue->year);
	$fileName = $dom->createElement('fileName', $catalogue->fileName);
	$metadata->appendChild($heading);
	$metadata->appendChild($year);
	$metadata->appendChild($fileName);
	$rootElement->appendChild($metadata);
	foreach($data as $item) {
		$itemElement = $dom->createElement('item');
		$itemElement = fillDOMItem($itemElement, $item, $dom);
		$rootElement->appendChild($itemElement);
	}
	$dom->appendChild($rootElement);
	$result = $dom->saveXML();
	$handle = fopen($folderName.'/'.$catalogue->fileName.'.xml', "w");
	fwrite($handle, $result, 3000000);
}

/* 
Diese Funktion ist schrecklich kompliziert. Man müsste sie in mehrere aufteilen oder 
generelle Anweisungen für die Verarbeitung der PHP-Objekte hinterlegen.
 */
 
function fillDOMItem($itemElement, $item, $dom) {
	foreach($item as $key => $value) {
		// Fall 1: Variable ist ein einfacher Wert
		if(is_array($value) == FALSE) {
			$value = replaceAmp($value);
			$itemProperty = $dom->createElement($key, $value);
			$itemElement = appendNodeUnlessVoid($itemElement, $itemProperty);
		}
		//Fall 2: Variable ist ein nummeriertes Array
		elseif(isset($value[0])) {
			//Fall 2.1: Variable ist ein Array aus einfachen Werten
			if(is_string($value[0]) or is_integer($value[0])) {
				$separatedString = implode(';', $value);
				$itemArrayProperty = $dom->createElement($key, $separatedString);
				$itemElement = appendNodeUnlessVoid($itemElement, $itemArrayProperty);
			}
			//Fall 2.2: Variable ist ein Array aus Objekten
			elseif(is_object($value[0])) {
				$itemObjectProperty = $dom->createElement($key);
				foreach($value as $object) {
					$nameObject = get_class($object);
					$objectElement = $dom->createElement($nameObject);
					foreach($object as $objectKey => $objectValue) {
						//Fall 2.2.1: Variable im Objekt ist ein Array
						if(is_array($objectValue)) {
							$objectVariable = $dom->createElement($objectKey);
							$collectNonAssociative = array();
							foreach($objectValue as $arrayKey => $arrayValue) {
								//Fall 2.2.1.1: Variable im Objekt ist ein assoziatives Array
								if(is_string($arrayKey)) {
									$objectArrayElement = $dom->createElement($arrayKey, $arrayValue);
									$objectVariable = appendNodeUnlessVoid($objectVariable, $objectArrayElement);
								}
								//Fall 2.2.1.2: Variable im Objekt ist ein numerisches Array
								elseif(is_int($arrayKey)) {
									$collectNonAssociative[] = $arrayValue;
								}
							}
							if(isset($collectNonAssociative[0])) {
								$separatedList = implode(';', $collectNonAssociative);
								$objectVariable = $dom->createElement($objectKey, $separatedList);
							}
							$objectElement = appendNodeUnlessVoid($objectElement, $objectVariable);
						}
						//Fall 2.2.2: Variable im Objekt ist ein Integer oder String
						elseif(is_int($objectValue) or is_string($objectValue)) {
							$objectVariable = $dom->createElement($objectKey, $objectValue);
							$objectElement = appendNodeUnlessVoid($objectElement, $objectVariable);
						}
					}
					$itemObjectProperty->appendChild($objectElement);
				}
				$itemElement = appendNodeUnlessVoid($itemElement, $itemObjectProperty);
			}
		}
		//Fall 3: Variable ist ein assoziatives Array
		else {
			$itemArrayProperty = $dom->createElement($key);
			foreach($value as $keyAssoc => $valueAssoc) {
				$valueAssoc = replaceAmp($valueAssoc);
				$itemArrayContent = $dom->createElement($keyAssoc, $valueAssoc);
				$itemArrayProperty = appendNodeUnlessVoid($itemArrayProperty, $itemArrayContent);
			}
			$itemElement = appendNodeUnlessVoid($itemElement, $itemArrayProperty);
		}
	}
	return($itemElement);
}

function appendNodeUnlessVoid($parent, $child) {
	if($child->nodeValue != '') {
		$parent->appendChild($child);
	}
	return($parent);
}

?>