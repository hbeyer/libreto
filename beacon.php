<?php
session_start();
include('classDefinition.php');
include('settings.php');
include('encode.php');
include('class_geoDataArchive.php');
include('class_reference.php');
include('class_beacon_repository.php');
include('addBeacon.php');
include('makeRDF.php');
$repository = new beacon_repository;
$_SESSION['beaconRepository'] = $repository;
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
				<h2>5. Anreicherung mit biographischen Links</h2>
				
	<?php
		$test1 = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1 and $_SESSION['annotation'] == 1 and $_SESSION['folder'] == 1 and $_SESSION['geoData'] == 1 and $_SESSION['storageID'] == 1) {
			$test1 = 1;
		}
		
		$test2 = 0;
		if(isset($_POST['beaconPosted'])) {
			$test2 = 1;
		}

		if($test1 == 1) {
			if($test2 == 0) {
				echo '<p>Auf der Seite &bdquo;Personen&rdquo; werden Links zu weiterf&uuml;hrenden Informationen soweit vorhanden angezeigt. Wenn Sie bestimmte Nachweissysteme ganz ausschlie&szlig;en wollen, k&ouml;nnen Sie sie hier abw&auml;hlen.</p>';
				echo '<form action="beacon.php" method="post">';
				foreach($_SESSION['beaconRepository']->beacon_sources as $key => $value) {
                    if (!in_array($value['label'], $_SESSION['beaconRepository']->missingFiles)) {
					    echo '
					    <div class="checkbox">
						    <label><input type="checkbox" name="'.$key.'" checked="checked">'.$value['label'].'</label>
					    </div>';
                    }
				}
				echo '
					<input type="hidden" name="beaconPosted">
					<input type="submit" name="Abschicken" />';
				echo '
				</form>';
			}
			elseif($test2 == 1) {
				$selectedBeacon = array();
				foreach($_POST as $key => $value) {
					if(isset($_SESSION['beaconRepository']->beacon_sources[$key])) {
						if($value == 'on') {
							$selectedBeacon[] = $key;
						}
					}
				}
				
				$dataString = file_get_contents($_SESSION['folderName'].'/dataPHP');
				$data = unserialize($dataString);
				unset($dataString);
				
                if ($_SESSION['beaconRepository']->missingFiles != array()) {
                    echo '<p>Nicht geladen: '.implode(', ', $_SESSION['beaconRepository']->missingFiles).'</p>';
                }
                $data = addBeacon($data, $_SESSION['beaconRepository']);

                // Export the data to RDF (RdfXML and Turtle)
                $catalogue = unserialize($_SESSION['catalogueObject']);
                saveRDF($data, $catalogue);

				$_SESSION['beacon'] = 1;

				$serialize = serialize($data);
				file_put_contents($_SESSION['folderName'].'/dataPHP', $serialize);
				
				echo '<p>Die Anreicherung mit biographischen Links war erfolgreich.<br>
				Weiter zur <a href="select.php">Feldauswahl</a>.</p>';
			}
		}
		
	?>
				
			</div>
		</body>
</html>
