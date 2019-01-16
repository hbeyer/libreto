<?php
include('classDefinition.php');
include('settings.php');
include('encode.php');
include('loadCSV.php');
include('loadXML.php');
include('makeCSV.php');

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
$_SESSION['beaconRepository'] = NULL;

// Create the necessary directories if not already there
$directories = array('user', 'beaconFiles', 'geoDataArchive', 'upload', 'upload/files', 'download');
foreach($directories as $folder) {
	if(is_dir($folder) == FALSE) {
		mkdir($folder, 0777);
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
				
				//If the Butten "Laden" has been pushed
				if(isset($_POST['filePosted'])) {			
					$_SESSION['fileName'] = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_FILENAME));
					$_SESSION['extension'] = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
					$allowedExtensions = array('csv', 'xml');
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
							$valid = validateCSV('upload/files/'.$_SESSION['fileNameInternal'].'.csv', 40);
							if($valid == 1) {
								$data = loadCSV('upload/files/'.$_SESSION['fileNameInternal'].'.'.$_SESSION['extension']);
								$serialize = serialize($data);
								file_put_contents('upload/files/dataPHP-'.$_SESSION['fileNameInternal'], $serialize);
								$_SESSION['store'] = 1;
								echo 'Import war erfolgreich.<br /><a href="annotate.php">Weiter zur Metadatenaufnahme</a>';
							}
							else {
								unlink('upload/files/'.$_SESSION['fileNameInternal'].'.csv');
								die('Fehler beim Import: '.$valid);
							}
						}
						elseif($_SESSION['extension'] == 'xml') {
							$valid = validateXML('upload/files/'.$_SESSION['fileNameInternal'].'.xml', 'uploadXML.xsd', 'mods-3-4.xsd');
							if($valid == 1) {
								$data = loadXML('upload/files/'.$_SESSION['fileNameInternal'].'.'.$_SESSION['extension']);
								$serialize = serialize($data);
								file_put_contents('upload/files/dataPHP-'.$_SESSION['fileNameInternal'], $serialize);
								$_SESSION['store'] = 1;
								echo 'Import war erfolgreich.<br /><a href="annotate.php">Weiter zur Metadatenaufnahme</a>';								
							}
							elseif($valid == 'mods') {
								//Deleting older files from folder "download"
								array_map('unlink', glob('download/*'));						
								transformMODS($_SESSION['fileNameInternal']);
								if(file_exists('download/'.$_SESSION['fileNameInternal'].'.xml')) {
									$data = loadXML('download/'.$_SESSION['fileNameInternal'].'.xml');
									makeCSV($data, 'download', $_SESSION['fileNameInternal']);
									unlink('upload/files/'.$_SESSION['fileNameInternal'].'.xml');
									echo 'Es wurde eine valide MODS-Datei erkannt und konvertiert.<br />Sie k&ouml;nnen mit <a href="download/'.$_SESSION['fileNameInternal'].'.xml" target="_blank">dieser XML-Datei</a> oder <a href="download/'.$_SESSION['fileNameInternal'].'.csv" target="_blank">dieser CSV-Datei</a> weiterarbeiten.';
								}
								else {
									die('Fehler beim Konvertieren der MODS-Datei.');
								}
							}
							else {
								unlink('upload/files/'.$_SESSION['fileNameInternal'].'.xml');
								die('Fehler beim Import: '.$valid);
							}
						}
					}
				}
				//If the page has been loaded without pushing the button "Laden"
				else {
					echo '
				<p>
					Daten k&ouml;nnen in folgenden Formaten hochgeladen werden:
					<ul>
						<li><b>CSV</b>: Ein Dokument im CSV-Format kann mit einem Tabellenkalkulationsprogramm bearbeitet werden. Sie k&ouml;nnen dazu <a href="vorlage.csv" target="_blank">diese Vorlage</a> verwenden. Eine <a href="Dokumentation_CSV.doc" target="_blank">Dokumentation</a> der einzelnen Felder liegt ebenfalls vor, ebenso ein Dokument mit <a href="example.csv" target="_blank">Beispieldaten</a>.</li>
						<li><b>XML</b>: Dokumente, die gegen das <a href="uploadXML.xsd" target="_blank">projekteigene Schema</a> validieren, k&ouml;nnen direkt hochgeladen werden. Dokumente im MODS-Format (dazu <a href="mods-3-4.xsd" target="_blank">dieses Schema</a>) werden in das projekteigene Format konvertiert und k&ouml;nnen dann weiterbearbeitet werden.</li>
					</ul>
				</p>';
				}
		
				?>
				</p>
			</div>
		</body>
</html>
