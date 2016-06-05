<?php

/* 
The following defines the fields which can be indexed and displayed. Each field listed in $indexFields can be 
given as $field to the function makeIndex and inserted in setConfiguration.php under $catalogue->facets 
to generate a separata page.
 */

$normalFields = array('id', 'pageCat', 'imageCat', 'numberCat', 'itemInVolume', 'titleCat', 'titleBib', 'titleNormalized', 'publisher', 'year', 'format', 'histSubject', 'mediaType', 'bound', 'comment', 'digitalCopy');
$personFields = array('persName', 'gnd', 'gender', 'role');
$placeFields = array('placeName', 'getty', 'geoNames');
$arrayFields = array('languages', 'subjects', 'genres');
$workFields = array('titleWork', 'systemWork', 'idWork');
$manifestationFields = array('systemManifestation');
$originalItemFields = array('institutionOriginal', 'shelfmarkOriginal', 'provenanceAttribute', 'targetOPAC', 'searchID');
// The following values do not correspond to a field, but they can be submitted to the function makeIndex
$virtualFields = array('catSubjectFormat');

$indexFields = array_merge($normalFields, $personFields, $placeFields, $arrayFields, $workFields, $manifestationFields, $originalItemFields, $virtualFields);

// The following fields are displayed with miscellanies as unordered lists
$volumeFields = array('numberCat', 'catSubjectFormat');

// The following fields get additional word clouds or doughnuts if they are selected
$wordCloudFields = array('publisher', 'year', 'format', 'histSubject', 'subjects', 'genres', 'mediaType', 'persName', 'gnd', 'role', 'placeName', 'languages', 'systemManifestation', 'institutionOriginal', 'shelfmarkOriginal', 'provenanceAttribute');
$doughnutFields = array('persName', 'gender', 'format', 'histSubject', 'subjects', 'genres', 'mediaType', 'languages', 'systemManifestation', 'institutionOriginal', 'provenanceAttribute', 'bound');

// Checkbox-Fields
$checkboxFields = array('numberCat', 'catSubjectFormat', 'shelfmarkOriginal', 'persName', 'gender', 'year', 'subjects', 'histSubject', 'genres', 'languages', 'placeName', 'publisher', 'format', 'mediaType', 'bound', 'systemManifestation', 'institutionOriginal', 'provenanceAttribute', 'pageCat', 'titleWork');

?>
