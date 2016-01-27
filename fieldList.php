<?php

/* 
The following defines the fields which can be indexed and displayed. Each field listed in $indexFields can be 
given as $field to the function makeIndex and inserted in setConfiguration.php under $catalogue->facets 
to generate a separata page.
The other variables are used by the funktion makeIndex to assign a "collect"-function.
 */

$normalFields = array('id', 'pageCat', 'imageCat', 'numberCat', 'itemInVolume', 'bibliographicalLevel', 'titleCat', 'titleBib', 'titleNormalized', 'publisher', 'year', 'format', 'histSubject', 'subject', 'genre', 'mediaType', 'bound', 'comment', 'digitalCopy');
$personFields = array('persName', 'gnd', 'role');
$placeFields = array('placeName', 'getty', 'geoNames');
$arrayFields = array('language');
$workFields = array('titleWork', 'systemWork');
$manifestationFields = array('systemManifestation');
$originalItemFields = array('institutionOriginal', 'shelfmarkOriginal', 'provenanceAttribute');

$indexFields = array_merge($normalFields, $personFields, $placeFields, $arrayFields, $workFields, $manifestationFields, $originalItemFields);
$indexFields[] = 'cat';

?>