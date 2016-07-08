<?php
include('classDefinition.php');
include('settings.php');
include('encode.php');
include('loadFile.php');

session_start();
//The following variables control the steps taken by users
$_SESSION['upload'] = 0;
$_SESSION['store'] = 0;
$_SESSION['annotation'] = 0;
$_SESSION['copiedToFolder'] = 0;
$_SESSION['folder'] = 0;
$_SESSION['geoData'] = 0;
$_SESSION['storageID'] = 0;
$_SESSION['beacon'] = 0;
$_SESSION['fieldSelection'] = 0;

//The following variables contain crucial metadata
$_SESSION['fileName'] = '';
$_SESSION['fileNameInternal'] = '';
$_SESSION['extension'] = '';
$_SESSION['unidentifiedPlaces'] = array();
$_SESSION['catalogueObject'] = NULL;
$_SESSION['folderName'] = '';

// Create the necessary directories if not already there
$directories = array('user', 'beaconFiles', 'geoDataArchive', 'upload', 'upload/files');
foreach($directories as $folder) {
	if(is_dir($folder) == FALSE) {
		mkdir($folder, 0700);
	}
}

//Protect the upload folder
$htaccess = "Order Deny,Allow\r\nDeny from All";
$fileName = 'upload/.htaccess';
$datei = fopen($fileName,"w");
fwrite($datei, $htaccess, 2000);
fclose($datei);

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
						<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
						<!-- Der Name des Input Felds bestimmt den Namen im $_FILES Array -->
						<input name="userfile" type="file" />
						<input type="hidden" name="filePosted" />
						<input type="submit" value="Laden" />
					</div>
				</form>
				<p>
				<?php
		
				if(isset($_POST['filePosted'])) {					
					$_SESSION['fileName'] = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME));
					$_SESSION['extension'] = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
					$allowedExtensions = array('csv');
					if(in_array($_SESSION['extension'], $allowedExtensions) == FALSE) {
						die('Ung&uuml;ltige Dateiendung.');
					}
					$maxSize = 2000*1024; // Maximum size of the file
					if($_FILES['userfile']['size'] > $maxSize) {
						die('Maximale erlaubte Dateigr&ouml;&szlig;e: 2 MB');
					}
					elseif($_FILES['userfile']['size'] == 0) {
						die('Fehler: Dateigr&ouml;&szlig;e 0 MB');
					}	
					else {
						$_SESSION['fileNameInternal'] = makeUploadName($_SESSION['fileName']);
						while(file_exists('upload/files/'.$_SESSION['fileNameInternal'])) {
							$_SESSION['fileNameInternal'] = makeUploadName($_SESSION['fileName']);
						}
						
						move_uploaded_file($_FILES['userfile']['tmp_name'], 'upload/files/'.$_SESSION['fileNameInternal'].'.'.$_SESSION['extension']);
						$_SESSION['upload'] = 1;
						echo 'Upload war erfolgreich.<br/>';
						
						if($_SESSION['extension'] == 'csv') {
							//validateCSV has to be called with the minimal number of columns as second argument
							$valid = validateCSV('upload/files/'.$_SESSION['fileNameInternal'].'.'.'csv', 40);
							if($valid == 1) {
								$data = loadCSV('upload/files/'.$_SESSION['fileNameInternal'].'.'.$_SESSION['extension']);
								$serialize = serialize($data);
								file_put_contents('upload/files/dataPHP-'.$_SESSION['fileNameInternal'], $serialize);
								$_SESSION['store'] = 1;
								echo 'Import war erfolgreich.<br /><a href="annotate.php">Weiter zur Metadatenaufnahme</a>';
							}
							else {
								unlink('upload/files/'.$_SESSION['fileNameInternal'].'.'.'csv');
								die('Fehler beim Import: '.$valid);
							}
						}
						
						
					}
				}
				
				function makeUploadName($string) {
					$salt = '07zhsuioedfzha87';
					$saltedString = $salt.$string.date('U');
					$name = hash('sha256', $saltedString);
					$name = substr($name, 0, 12);
					return($name);
				}		
		
				?>
				</p>
			</div>
		</body>
</html>