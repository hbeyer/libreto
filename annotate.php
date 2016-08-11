<?php
include('classDefinition.php');
include('settings.php');
include('encode.php');
include('makeXML.php');
include('makeCSV.php');
session_start();
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
				<h2>2. Aufnahme von Metadaten zur Sammlung</h2>
				
	<?php
	
		$test1 = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1) {
			$test1 = 1;
		}
		$test2 = 0;
		if(isset($_POST['metadataPosted'])) {
			$test2 = 1;
		}
		
		
		if($test1 == 0) {
			echo '<p>Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
		}
		elseif($test1 == 1 and $test2 == 0) {
			
			$suggestions = array('heading' => '', 'owner' => '', 'ownerGND' => '', 'year' => '', 'fileName' => '', 'title' => '', 'institution' => '', 'shelfmark' => '', 'base' => '', 'description' => '', 'geoBrowserStorageID' => '');
			if($_SESSION['catalogueObject'] != NULL) {
				$catalogue = unserialize($_SESSION['catalogueObject']);
				foreach($catalogue as $key => $value) {
					if(isset($suggestions[$key])) {
						$suggestions[$key] = $value;
					}
				}
			}
			
			echo '
			<form class="form-horizontal"  action="annotate.php" method="post">';
			echo '
				<div class="form-group">
					<label class="control-label col-sm-3" for="heading">Name der Sammlung</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="heading"  maxlength="140" required placeholder="Beispiel: Bibliothek des Heinrich Bernhard Schrader von Schliestedt" value="'.$suggestions['heading'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="owner">Besitzende Person oder Institution</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="owner"  maxlength="140" required placeholder="Beispiel: Schrader von Schliestedt, Heinrich Bernhard" value="'.$suggestions['owner'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="ownerGND">GND der Person oder Institution</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="ownerGND" maxlength="12" pattern="[0-9X-]{4,12}" placeholder="Beispiel: 117024988" value="'.$suggestions['ownerGND'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="year">Datum der Sammlung (Jahr)</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="year" pattern="[0-9]{4}" required placeholder="Erscheinungsjahr des Katalogs oder Stand der Sammlung" value="'.$suggestions['year'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="fileName">Dateiname</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="fileName" pattern="[A-Za-z0-9-_]{4,28}" required placeholder="Beispiel: schliestedt" value="'.$suggestions['fileName'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="title">Titel des Altkatalogs</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="title" maxlength="1000" placeholder="Beispiel: Apparatus librorum Henrici Bernhardi de Schliestedt" value="'.$suggestions['title'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="institution">Besitzende Institution</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="institution" maxlength="100" placeholder="Beispiel: HAB Wolfenb&uuml;ttel" value="'.$suggestions['institution'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="shelfmark">Signatur</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="shelfmark" maxlength="40" placeholder="Beispiel: M: Bc 1982" value="'.$suggestions['shelfmark'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="base">Basis-URL f&uuml;r Links</label>
						<div class="col-sm-9">
						<input type="text" class="form-control" name="base" type="url" maxlength="280" placeholder="Beispiel: http://diglib.hab.de/drucke/bc-1982/start.htm?image=" value="'.$suggestions['base'].'">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-sm-3" for="description">Abstract</label>
						<div class="col-sm-9">
						<textarea class="form-control" rows="3" name="description" maxlength="1400" placeholder="Kurzer Einf&uuml;hrungstext">'.$suggestions['description'].'</textarea>
					</div>
				</div>				
				<div class="form-group">        
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-default">Abschicken</button>
					</div>
				</div>
				';
				echo '
				<input type="hidden" name="metadataPosted">
			</form>';
		}
		elseif($test1 == 1 and $test2 == 1) {
			if($_SESSION['catalogueObject'] != NULL) {
				$catalogue = unserialize($_SESSION['catalogueObject']);
			}
			else {
				$catalogue = new catalogue();
			}
			$catalogue->heading = $_POST['heading'];
			$catalogue->owner = $_POST['owner'];
			$catalogue->ownerGND = $_POST['ownerGND'];
			$catalogue->year = $_POST['year'];
			$catalogue->fileName = $_POST['fileName'];
			$catalogue->title = $_POST['title'];
			$catalogue->institution = $_POST['institution'];
			$catalogue->shelfmark = $_POST['shelfmark'];
			$catalogue->base = $_POST['base'];
			$catalogue->description = $_POST['description'];
			
			$_SESSION['annotation'] = 1;
			$_SESSION['catalogueObject'] = serialize($catalogue);
			
			$folderName = 'user/'.$catalogue->fileName;
			$_SESSION['folderName'] = $folderName;
			if(is_dir($folderName) == FALSE) {
				mkdir($folderName, 0700);
			}
			else {
				array_map('unlink', glob($folderName.'/*.html'));
			}
			
			if($_SESSION['copiedToFolder'] == 0) {
				copy ('proprietary.css', $folderName.'/proprietary.css');
				copy ('proprietary.js', $folderName.'/proprietary.js');
				copy ('chart.js', $folderName.'/chart.js');
				copy ('upload/files/dataPHP-'.$_SESSION['fileNameInternal'], $folderName.'/dataPHP');
			
				unlink('upload/files/dataPHP-'.$_SESSION['fileNameInternal']);
				unlink('upload/files/'.$_SESSION['fileNameInternal'].'.'.$_SESSION['extension']);
				$_SESSION['copiedToFolder'] = 1;
			}
			
			if(file_exists($folderName.'/dataPHP')) {
				$_SESSION['folder'] = 1;
				$dataString = file_get_contents('bahnsen/dataPHP');
				$data = unserialize($dataString);
				unset($dataString);
				saveXML($data, $catalogue, $folderName);
				makeCSV($data, $folderName, $catalogue->fileName);
				echo '<p>Die Metadaten wurden gespeichert.<br />';
				echo 'Weiter zur <a href="geodata.php">Geodatenanreicherung</a>.</p>';
			}
			else {
				echo '<p>Es ist ein Fehler aufgetreten. Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
			}
		}
		
	?>
				
			</div>
		</body>
</html>