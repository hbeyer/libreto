<?php
session_start();
$_SESSION['upload'] = 0;
$_SESSION['fileName'] = '';
$_SESSION['ending'] = '';
$_SESSION['store'] = 0;
$_SESSION['geoData'] = 0;
$_SESSION['beacon'] = 0;
$_SESSION['annotation'] = 0;
$_SESSION['catalogueObject'] = NULL;
$_SESSION['folder'] = 0;
$_SESSION['folderName'] = '';
$_SESSION['fieldSelection'] = 0;

include('classDefinition.php');
include('encode.php');
include('loadFile.php');

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
				<h2>1. Hochladen der Datei</h2>
				
				<!-- Die Encoding-Art enctype MUSS wie dargestellt angegeben werden -->
				<form  enctype="multipart/form-data" action="load.php" method="POST">
					<div class="form-group">
						<!-- MAX_FILE_SIZE muss vor dem Dateiupload Input Feld stehen -->
						<input type="hidden" name="MAX_FILE_SIZE" value="300000" />
						<!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
						<input name="userfile" type="file" />
						<input type="submit" value="Laden" />
					</div>
				</form>
	
	<?php
		
		if(isset($_FILES['userfile'])) {
			$uploadDirectory = 'C:\xampp\tmp';
			$_SESSION['fileName'] = $_FILES['userfile']['name'];
			$_SESSION['ending'] = getEnding($_SESSION['fileName']);
			$_SESSION['bareName'] = getFileName($_SESSION['fileName']);
			$acceptedEndings = array('csv');
			$uploadFile = $uploadDirectory.basename($_FILES['userfile']['name']);
			
			if(in_array($_SESSION['ending'], $acceptedEndings)) {
				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $_SESSION['fileName'])) {
					$_SESSION['upload'] = 1;
					
					$data = loadCSV($_SESSION['fileName']);
					$serialize = serialize($data);
					file_put_contents('uploadedData', $serialize);
					
					if(filesize('uploadedData') > 0) {
						$_SESSION['store'] = 1;
						echo '<p>Upload und Import der Datei waren erfolgreich.<br>
						<a href="enrich.php">Weiter zur Datenanreicherung</a></p>';
					}
					else {
						echo '<p>Fehler: Gr&ouml;&szlig;e der importierten Datei ist 0.</p>';
					}
					
				} 
			else {
				echo '<p>Fehler: Datei konnte nicht gespeichert werden.</p>';
			}
			}
		}
		
		function getEnding($fileName) {
			$parts = explode('.', $fileName);
			if(isset($parts[1])) {
				return(strtolower($parts[1]));
			}
		}
		
		function getFileName($fileName) {
			$parts = explode('.', $fileName);
			if(isset($parts[0])) {
				return(strtolower($parts[0]));
			}
		}		
		
		
	?>
				
			</div>
		</body>
</html>