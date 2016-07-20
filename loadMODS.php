<?php

include('loadXML.php');

function transformMODS($path) {
	$fileName = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME));
	$xml = new DOMDocument;
	$xml->load($path);
	$xsl = new DOMDocument;
	$xsl->load('transformMODS.xsl');
	$proc = new XSLTProcessor();
	$proc->importStyleSheet($xsl);
 	$result = $proc->transformToXML($xml);
	$handle = fopen('downloads/'.$fileName.'.xml', "w");
	fwrite($handle, $result, 3000000);
}

?>