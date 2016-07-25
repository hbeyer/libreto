<?php
session_start();
include('classDefinition.php');
include('settings.php');
include('encode.php');
include('makeGeoDataArchive.php');
include('beaconSources.php');
include('storeBeacon.php');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Visualisierung historischer Sammlungen</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="jsfunctions.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		
		
		
	</head>
		<body>
			<div class="container">
				<h1>Visualisierung historischer Sammlungen</h1>
				<h2>3. Anreicherung mit Geodaten</h2>
				
	<?php
		
		$test1 = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1 and $_SESSION['annotation'] == 1 and $_SESSION['folder'] == 1) {
			$test1 = 1;
		}
		
		if(isset($_POST['missingGeoDataPosted'])) {
			addPostedDataToArchive();
		}
		
		if($test1 == 0) {
			echo '<p>Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
		}
		elseif($test1 == 1) {
			$archiveGeoNames = new GeoDataArchive();
			$archiveGeoNames->loadFromFile('geoNames');
			$archiveGetty = new GeoDataArchive();
			$archiveGetty->loadFromFile('getty');
			$archiveGND = new GeoDataArchive();
			$archiveGND->loadFromFile('gnd');			
			
			$dataString = file_get_contents($_SESSION['folderName'].'/dataPHP');
			$data = unserialize($dataString);
			unset($dataString);
			
			$unidentifiedPlaces = array();
			$placeFromArchive = '';
			$countWebDownloads = 0;
			
			foreach($data as $item) {
				foreach($item->places as $place) {
					
					$searchName = trim($place->placeName, '[]');

					$testUnidentified = preg_match('~^[sSoO]\.? ?[oOlL].?$|^[oO]hne Ort|[sS]ine [lL]oco|[oO]hne Angabe~', $searchName);
					if(strstr($searchName, 'fingiert') != FALSE) {
						$testUnidentified = 1;
					}
					elseif($searchName == '') {
						$testUnidentified = 1;
					}
					$testGeoData = 0;
					if($place->geoData['lat'] and $place->geoData['long']) {
						$testGeoData == 1;
					}
					
					if($testUnidentified == 1) {
						$place->placeName = 's. l.';
					}
					
					if($testUnidentified == 0 and $testGeoData == 0) {
						if($place->geoNames) {
							$placeFromArchive = $archiveGeoNames->getByGeoNames($place->geoNames);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archiveGeoNames
								->makeEntryFromGeoNames($place->geoNames, $userGeoNames);
								if($placeFromWeb) {
									$archiveGeoNames->insertEntryIfNew('geoNames', $place->geoNames, $placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}
						
						elseif($place->gnd) {
							$placeFromArchive = $archiveGND->getByGND($place->gnd);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archiveGND
								->makeEntryFromGNDTTL($place->gnd);
								if($placeFromWeb) {
									$archiveGND->insertEntryIfNew('gnd', $place->gnd, $placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}		
						
						elseif($place->getty) {
							$placeFromArchive = $archiveGetty->getByGetty($place->getty);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archiveGetty->makeEntryFromGetty($place->getty);
								if($placeFromWeb) {
									$archiveGetty->insertEntryIfNew('getty', $place->getty, $placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}
						
						else {
							$placeFromArchive = $archiveGeoNames->getByName($searchName);
						}
						if($placeFromArchive) {
							$place->geoData['lat'] = $placeFromArchive->lat;
							$place->geoData['long'] = $placeFromArchive->long;
						}
						elseif($testUnidentified == 0) {
							$unidentifiedPlaces[] = $searchName;
						}
					}
				}
			}
			$archiveGeoNames->saveToFile('geoNames');
			$archiveGetty->saveToFile('getty');
			$archiveGND->saveToFile('gnd');
			$serialize = serialize($data);
			file_put_contents($_SESSION['folderName'].'/dataPHP', $serialize);
			unset($data);
			$unidentifiedPlaces = array_unique($unidentifiedPlaces);
			$unidentifiedList = implode(', ', $unidentifiedPlaces);
			echo '<p>Es wurden '.$countWebDownloads.' neue Geodatens&auml;tze aus dem Web geladen.</p>';
			if($unidentifiedList) {
				$_SESSION['unidentifiedPlaces'] = $unidentifiedPlaces;
				$form = makeGeoDataForm($unidentifiedPlaces);
				echo '<p>Folgende Orte konnten nicht identifiziert werden. Sie k&ouml;nnen hier einen Identifier f&uuml;r jeden Ort nachtragen.<br/>
				Suchen Sie dazu auf <a href="http://www.geonames.org/" target="_blank">geoNames</a> oder in der <a href="http://swb.bsz-bw.de/DB=2.104" target="_blank">Gemeinsamen Normdatei</a> nach &bdquo;Geographikum als Schlagwort&rdquo;. Achten Sie bei der GND darauf, dass der gew&auml;hlte Datensatz Geodaten enth&auml;lt.</p>';
				echo $form;
				echo '<p>Ignorieren und weiter zum <a href="geoBrowser.php">Datenexport in den GeoBrowser</a></p>';
			} 
			else {
				$_SESSION['geoData'] = 1;
				echo '<p>Die Anreicherung mit Geodaten ist abgeschlossen.<br/>Weiter zum <a href="geoBrowser.php">Datenexport in den GeoBrowser</a></p>';
			}
		}


function makeGeoDataForm($unidentifiedPlaces) {
	$form = '
	<form class="form-horizontal" role="form" action="geodata.php" method="post">';
	$count = 0;
	foreach($unidentifiedPlaces as $city) {
		$form .= makeGeoDataFormRow($count, $city);
		$count ++;
	}
	$form .= '
		<input type="hidden" name="missingGeoDataPosted">
		<button type="submit" class="btn btn-default">Abschicken</button>
	</form>';
	return($form);
}

function makeGeoDataFormRow($cityID, $city) {
	$row = '
    <div class="form-group">
        <span class="col-md-2 control-label">'.$city.'</span>
        <div class="col-md-10">
            <div class="form-group row">
                <label for="geoNames_'.$cityID.'" class="col-md-1 control-label">geoNames</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" pattern="[0-9]{5,9}"  name="geoNames_'.$cityID.'" placeholder="Beispiel: 2956147">
                </div>
                <label for="gnd_'.$cityID.'" class="col-md-1 control-label">GND</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" pattern="[0-9X-]{4,10}" name="gnd_'.$cityID.'" placeholder="Beispiel: 7576656-5">
                </div>				
            </div>
        </div>
    </div>';
	return($row);
}

function addPostedDataToArchive() {
	include('settings.php');
	$archiveGeoNames = new GeoDataArchive();
	$archiveGeoNames->loadFromFile('geoNames');
	$archiveGetty = new GeoDataArchive();
	$archiveGetty->loadFromFile('getty');
	$archiveGND = new GeoDataArchive();
	$archiveGND->loadFromFile('gnd');	
	$count = 0;
	foreach($_SESSION['unidentifiedPlaces'] as $city) {
		$placeFromWeb = NULL;
		if(isset($_POST['geoNames_'.$count])) {
			$geoNames = $_POST['geoNames_'.$count];
			if($geoNames != '') {
				$placeFromWeb = $archiveGeoNames->makeEntryFromGeoNames($geoNames, $userGeoNames);
				//$placeFromWeb->label = $_SESSION['unidentifiedPlaces'][$count];
				$placeFromWeb->label = $city;
				$archiveGeoNames->insertEntry($placeFromWeb);
			}
		}
		if(isset($_POST['gnd_'.$count])) {
			$gnd = $_POST['gnd_'.$count];
			if($gnd != '') {
				$placeFromWeb = $archiveGeoNames->makeEntryFromGNDTTL($gnd);
				//$placeFromWeb->label = $_SESSION['unidentifiedPlaces'][$count];
				$placeFromWeb->label = $city;
				$archiveGND->insertEntry($placeFromWeb);
			}
		}		
		elseif(isset($_POST['getty_'.$count])) {
			$getty = $_POST['getty_'.$count];
			if($getty != '') {
				$placeFromWeb = $archiveGetty->makeEntryFromGetty($getty);
				//$placeFromWeb->label = $_SESSION['unidentifiedPlaces'][$count];				
				$placeFromWeb->label = $city;				
				$archiveGetty->insertEntry($placeFromWeb);
			}
		}
		unset($_SESSION['unidentifiedPlaces'][$count]);
		$count++;
	}
	$archiveGeoNames->saveToFile('geoNames');
	$archiveGetty->saveToFile('getty');
	$archiveGND->saveToFile('gnd');
}
		
		
	?>
				
			</div>
		</body>
</html>