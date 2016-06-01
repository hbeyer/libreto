<?php
session_start();
include('classDefinition.php');
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
					if($place->placeName != 's. l.') {
						
						if($place->geoNames) {
							$placeFromArchive = $archiveGeoNames->getByGeoNames($place->geoNames);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archiveGeoNames
								->makeEntryFromGeoNames($place->geoNames);
								if($placeFromWeb) {
									$archiveGeoNames->insertEntryIfNew($placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}
						
						elseif($place->getty) {
							$placeFromArchive = $archiveGetty->getByGetty($place->getty);
							var_dump($placeFromArchive);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archiveGetty->makeEntryFromGetty($place->getty);
								if($placeFromWeb) {
									$archiveGetty->insertEntry($placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}
						
						else {
							$placeFromArchive = $archiveGeoNames->getByName($place->placeName);
						}
						if($placeFromArchive) {
							$place->geoData['lat'] = $placeFromArchive->lat;
							$place->geoData['long'] = $placeFromArchive->long;
						}
						elseif($place->placeName != 's.l.') {
							$unidentifiedPlaces[] = $place->placeName;
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
				Suchen Sie dazu auf <a href="http://www.geonames.org/" target="_blank">geoNames</a> oder im <a href="http://www.getty.edu/research/tools/vocabularies/tgn/">Getty Thesaurus of Geographic Names</a>.</p>';
				echo $form;
			} 
			else {
				$_SESSION['geoData'] = 1;
				echo '<p>Die Anreicherung mit Geodaten ist abgeschlossen.<br/>Weiter zum <a href="geoBrowser.php">Datenexport in den GeoBrowser</a></p>.';
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
                    <input type="text" class="form-control" name="geoNames_'.$cityID.'" placeholder="7-stellige Nummer">
                </div>
                <label for="getty_'.$cityID.'" class="col-md-1 control-label">Getty</label>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="getty_'.$cityID.'" placeholder="7-stellige Nummer">
                </div>
            </div>
        </div>
    </div>';
	return($row);
}

function addPostedDataToArchive() {
	$archiveGeoNames = new GeoDataArchive();
	$archiveGeoNames->loadFromFile('geoNames');
	$archiveGetty = new GeoDataArchive();
	$archiveGetty->loadFromFile('getty');
	$count = 0;
	foreach($_SESSION['unidentifiedPlaces'] as $city) {
		$placeFromWeb = NULL;
		if(isset($_POST['geoNames_'.$count])) {
			$geoNames = $_POST['geoNames_'.$count];
			if($geoNames != '') {
				$placeFromWeb = $archiveGeoNames->makeEntryFromGeoNames($geoNames);
				$archiveGeoNames->insertEntry($placeFromWeb);
			}
		}
		elseif(isset($_POST['getty_'.$count])) {
			$getty = $_POST['getty_'.$count];
			if($getty != '') {
				$placeFromWeb = $archiveGetty->makeEntryFromGetty($getty);
				$archiveGetty->insertEntry($placeFromWeb);
			}
		}
		$count++;
	}
	$archiveGeoNames->saveToFile('geoNames');
	$archiveGetty->saveToFile('getty');
}
		
		
	?>
				
			</div>
		</body>
</html>