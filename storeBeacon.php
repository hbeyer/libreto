<?php

function arrayGND($data) {
	$gndArray = array();
	foreach($data as $item) {
		foreach($item->persons as $person) {
			if((in_array($person->gnd, $gndArray) == FALSE) and ($person->gnd != '')) {
				$gndArray[] = $person->gnd;
			}
		}
	}
	return($gndArray);
}

function storeBeacon($data, $folderName, $selectedBeacon = 'all') {
	include('beaconSources.php');
	if($selectedBeacon == 'all') {
		$selectedBeacon = $beaconKeys;
	}
	$gndArray = arrayGND($data);
	unset($data);
	
	$result = array();
	ini_set('user_agent','Herzog August Bibliothek, Dr. Hartmut Beyer');
	foreach($beaconSources as $key => $source) {
		if(in_array($key, $selectedBeacon)) {
			$beaconFile = file_get_contents($source['location']);
			$interimResult = array();
			foreach($gndArray as $gnd) {
				preg_match('%'.$gnd.'%', $beaconFile, $treffer);
				if(isset($treffer[0])) {
					$interimResult[] = $gnd;
				}
					unset($treffer);
			}
				unset($beaconFile);
			$result[$key] = $interimResult;
		}
	}
		
	$beaconData = new beaconData();
	$beaconData->date = date("Y-m-d H:i:s");
	foreach($beaconSources as $key => $source) {
		if(in_array($key, $selectedBeacon)) {
			$extract = new beaconExtract();
			$extract->label = $source['label'];
			$extract->key = $key;
			$extract->target = $source['target'];
			$extract->content = $result[$key];
			$beaconData->content[] = $extract;
		}
	}
	$serialize = serialize($beaconData);
	if($folderName == '') {
		file_put_contents('user/beaconStore', $serialize);
	}
	else {
		file_put_contents($folderName.'/beaconStore', $serialize);
	}
}

function addBeacon($data, $folderName) {
	$beaconString = file_get_contents($folderName.'/beaconStore');
	$beaconObject = unserialize($beaconString);
	foreach($data as $item) {
		foreach($item->persons as $person) {
			if($person->gnd != '') {
				foreach($beaconObject->content as $beaconExtract) {
					if(in_array($person->gnd, $beaconExtract->content)) {
						$person->beacon[] = $beaconExtract->key;
					}
				}
			}
		}
	}
	return($data);
}

?>