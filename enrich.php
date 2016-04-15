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
				<h2>2. Anreicherung mit Geodaten</h2>
				
	<?php
	
		$test = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1) {
			$test = 1;
		}
		
		if($test == 0) {
			echo '<p>Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
		}
		elseif($test == 1) {
			$archive = new GeoDataArchive();
			$archive->loadFromFile();
			
			$dataString = file_get_contents('uploadedData');
			$data = unserialize($dataString);
			unset($dataString);
			
			$unidentifiedPlaces = array();
			$placeFromArchive = '';
			$countWebDownloads = 0;
			
			foreach($data as $item) {
				foreach($item->places as $place) {
					if($place->placeName != 's. l.') {
						if($place->geoNames) {
							$placeFromArchive = $archive->getByGeoNames($place->geoNames);
							if($placeFromArchive == NULL) {
								$placeFromWeb = $archive->makeEntryFromGeoNames($place->geoNames);
								if($placeFromWeb) {
									$archive->insertEntryIfNew($placeFromWeb);
									$placeFromArchive = $placeFromWeb;
									$countWebDownloads++;
								}
							}
						}
						else {
							$placeFromArchive = $archive->getByName($place->placeName);
						}
						if($placeFromArchive) {
							$place->geoData['lat'] = $placeFromArchive->lat;
							$place->geoData['long'] = $placeFromArchive->long;
						}
						else {
							$unidentifiedPlaces[] = $place->placeName;
						}
				}
				}
			}
			$archive->saveToFile();
			$unidentifiedPlaces = array_unique($unidentifiedPlaces);
			$unidentifiedList = implode(', ', $unidentifiedPlaces);
			$_SESSION['geoData'] = 1;
			echo '<p>Die Anreicherung mit Geodaten ist abgeschlossen.<br>
			Es wurden '.$countWebDownloads.' neue Geodatens&auml;tze aus dem Web geladen';
			if($unidentifiedList) {
				echo '. Folgende Ortsnamen konnten nicht zugeordnet werden: '.$unidentifiedList;
			}
			echo '.</p>';
		}
		if($_SESSION['geoData'] == 1) {
			$test2 = isset($_POST['beaconPosted']);
			echo '<h2>3. Anreicherung mit biographischen Daten</h2>';
			if($test2 == FALSE) {
				echo '<p>Bitte w&auml;hlen Sie die Nachweissysteme ab, zu denen auf der Seite &bdquo;Personen&rdquo; keine Links angezeigt werden sollen.</p>';
				echo '<form action="enrich.php" method="post">';
				foreach($beaconSources as $key => $value) {
					echo '
					<div class="checkbox">
						<label><input type="checkbox" name="'.$key.'" checked="checked">'.$value['label'].'</label>
					</div>';
				}
				echo '
					<input type="hidden" name="beaconPosted">
					<input type="submit" name="Abschicken" />';
				echo '
				</form>';
			}
			else {
				$selectedBeacon = array();
				foreach($_POST as $key => $value) {
					if(in_array($key, $beaconKeys)) {
						if($value == 'on') {
							$selectedBeacon[] = $key;
						}
					}
				}
				$dataString = file_get_contents('uploadedData');
				$data = unserialize($dataString);
				unset($dataString);
				//Das Laden wurde aus Zeitgründen deaktiviert, die Daten kommen solange aus der Datei baconStore-new
				storeBeacon($data, '', 'new', $selectedBeacon);
				$data = addBeacon($data, '', 'new');
				$_SESSION['beacon'] = 1;
				echo '<p>Die Anreicherung mit biographischen Links war erfolgreich.<br>
				Weiter zur <a href="annotate.php">Metadatenaufnahme</a>.</p>';
			}
		}
		
		
	?>
				
			</div>
		</body>
</html>