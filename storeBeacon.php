<?php

date_default_timezone_set('Europe/Amsterdam');

function cacheBeacon($sources, $seconds) {
	$test = 0;
	// Calculate how much time has passed since the last update of the files
	$age = date('U') - filemtime('beaconFiles');
	if($age > $seconds) {
		$test = 1;
	}
	// Test whether all Beacon files are present in the folder beaconFiles
	foreach($sources as $key => $source) {
		if(file_exists('beaconFiles/'.$key) == FALSE) {
			$test = 1;
		}
	}
	// Download new files if necessary
	if($test == 1) {
		ini_set('user_agent','Herzog August Bibliothek, Dr. Hartmut Beyer');
		foreach($sources as $key => $source) {
			$beaconFile = file_get_contents($source['location']);
			file_put_contents('beaconFiles/'.$key, $beaconFile);
		}
	}
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
			$beaconFile = file_get_contents('beaconFiles/'.$key);
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
						if(in_array($beaconExtract->key, $person->beacon) == FALSE) {
							$person->beacon[] = $beaconExtract->key;
						}
					}
				}
			}
		}
	}
	return($data);
}

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

?>