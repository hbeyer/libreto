<?php
session_start();
include('classDefinition.php');
include('encode.php');
include('makeGeoDataArchive.php');
include('makeGeoDataSheet.php');
include('makeIndex.php');
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
				<h2>4. Export der Geodaten</h2>
				
	<?php
		
		$test1 = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1 and $_SESSION['annotation'] == 1 and $_SESSION['folder'] == 1 and $_SESSION['geoData'] = 1) {
			$test1 = 1;
		}
		
		$test2 = 0;
		if(isset($_POST['storageID'])) {
			if($_POST['storageID'] != '') {
				$test2 = 1;
			}
		}
		
		if($test1 == 0) {
			echo '<p>Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
		}
		
		elseif($test1 == 1 and $test2 == 0) {
			
			$dataString = file_get_contents($_SESSION['folderName'].'/dataPHP');
			$data = unserialize($dataString);
			unset($dataString);
			
			makeGeoDataSheet($data, $_SESSION['folderName'], 'KML');
			makeGeoDataSheet($data, $_SESSION['folderName'], 'CSV');
			
			echo '<p>Es wurden Geodatenbl&auml;tter f&uuml;r das Projekt in zwei verschiedenen Formaten erstellt:</p>';
			echo '<ul>';
			echo '<li><a href="'.$_SESSION['folderName'].'/printingPlaces.csv">printingPlaces.csv</a></li>';
			echo '<li><a href="'.$_SESSION['folderName'].'/printingPlaces.kml">printingPlaces.kml</a></li>';
			echo '</ul>';
			echo '<p>Um diese Daten im DARIAH GeoBrowser zu visualisieren und die Visualisierung zu verlinken, speichern Sie die <a href="'.$_SESSION['folderName'].'/printingPlaces.csv">CSV-Datei</a> auf Ihrer lokalen Festplatte ab.<br/>
			Dann gehen Sie zum <a href="http://geobrowser.de.dariah.eu/edit/" target="_blank">Datasheet Editor</a> und laden diese Datei mittels der Funktion &bdquo;import a local CSV file&rdquo; hoch.
			Sie haben anschlie&szlig;end die M&ouml;glichkeit, Ihre Daten einzusehen und gegebenefalls zu korrigieren.<br/>
			Mit dem Button &bdquo;Open Geo-Browser&rdquo; unten rechts gelangen Sie zur Kartenansicht. Die zugeh&ouml;rige Storage ID, eine 6-stellige Zahl, finden Sie rechts oben unter &bdquo;Magnetic Link&rdquo; und in der URL. Tragen Sie diese ID hier ein:
			<form class="form-horizontal" role="form" action="geoBrowser.php" method="post">
				<div class="form-group">
					<label class="control-label col-sm-2" for="heading">GeoBrowser Storage ID</label>
					<div class="col-sm-6">
						<input type="text" class="form-control" name="storageID" pattern="[0-9]{4,8}" maxlength="8" required>
					</div>
				</div>
				<input type="hidden" name="storageIDPosted">
				<button type="submit" class="btn btn-default">Abschicken</button>
			</form>
			</p>';
		}
		elseif($test1 == 1 and $test2 == 1) {
			$catalogue = unserialize($_SESSION['catalogueObject']);
			$catalogue->GeoBrowserStorageID = $_POST['storageID'];
			$_SESSION['storageID'] = 1;
			echo '<p>Der <a href="http://geobrowser.de.dariah.eu/?csv1=http%3A%2F%2Fgeobrowser.de.dariah.eu%2Fstorage%2F'.$_POST['storageID'].'" target="_blank">Link zum DARIAH GeoBrowser</a> ist gespeichert.<br/>
			Weiter zur Anreicherung mit <a href="beacon.php">biographischen Links</a>.</p>';
		}
		
		
	?>
				
			</div>
		</body>
</html>