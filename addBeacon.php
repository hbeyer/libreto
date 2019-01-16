<?php

//include('class_beacon_repository.php');
//include('classDefinition.php');

function addBeacon($data, $repository) {
    
    $gnds = array();
    foreach ($data as $item) {
        foreach ($item->persons as $person) {
            if ($person->gnd) {
                $gnds[] = $person->gnd;
            }
        }
    }
    $gnds = array_unique($gnds);
    $matches = $repository->getMatchesMulti($gnds);
    foreach ($data as $item) {
        foreach ($item->persons as $person) {
            if ($person->gnd) {
                if (!empty($matches[$person->gnd])) {
                    $person->beacon = $matches[$person->gnd];
                }
            }
        }
    }
    return($data);
}

/*
$dataString = file_get_contents('user/bahnsen/dataPHP');
$data = unserialize($dataString);
$repository = new beacon_repository;
$test = $repository->missingFiles();
var_dump($test);
*/

?>
