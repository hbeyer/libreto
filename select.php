<?php
include('classDefinition.php');
include('settings.php');
include('makeGeoDataSheet.php');
include('addToSOLR.php');
include('encode.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makeHead.php');
include('makeEntry.php');
include('makeCloudList.php');
include('makeDoughnutList.php');
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
				<h2>5. Auswahl der darzustellenden Felder</h2>
				
	<?php
	
		$test1 = 0;
		if($_SESSION['upload'] == 1 and $_SESSION['store'] == 1 and $_SESSION['geoData'] == 1 and $_SESSION['beacon'] == 1 and $_SESSION['annotation'] == 1 and $_SESSION['folder'] == 1) {
			$test1 = 1;
		}
		$test2 = 0;
		if(isset($_POST['fieldsPosted'])) {
			$test2 = 1;
		}
		
		
		if($test1 == 0) {
			echo '<p>Bitte fangen Sie <a href="load.php">von vorne</a> an.</p>';
		}
		elseif($test1 == 1 and $test2 == 0) {
			$checkboxes = makeSelectForm();
			echo '<p>Bitte w&auml;hlen Sie aus, wie die einzelnen Felder dargestellt werden sollen.</p>';
			echo '
			<form class="form-horizontal"  action="select.php" method="post">';
			echo '
				'.$checkboxes;
			echo 				'
				<div class="form-group">        
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-default">Abschicken</button>
					</div>
				</div>
				';
				echo '
				<input type="hidden" name="fieldsPosted">
			</form>';
		}
		elseif($test1 == 1 and $test2 == 1) {
			
			$_SESSION['fieldSelection'] = 1;
			$catalogue = unserialize($_SESSION['catalogueObject']);
			$catalogue = insertFacetsToCatalogue($catalogue, $_POST);
			
			$dataString = file_get_contents($_SESSION['folderName'].'/dataPHP');
			$data = unserialize($dataString);
			unset($dataString);
			
			echo '<p>';
			
			if($data) {
				echo 'Serialisierten Daten geladen.<br/>';
			}
					
			makeGeoDataSheet($data, $_SESSION['folderName'], 'KML');
			makeGeoDataSheet($data, $_SESSION['folderName'], 'CSV');
			
			if(file_exists($_SESSION['folderName'].'/printingPlaces.csv') and file_exists($_SESSION['folderName'].'/printingPlaces.kml')) {
				echo 'Geodaten-Sheets gespeichert.<br/>';
			}
			
			$fileNameSOLR = $_SESSION['folderName'].'/'.$catalogue->fileName;
			$SOLRArray = makeSOLRArray($data);
			$SOLRArray = addMetaDataSOLR($catalogue, $SOLRArray);
			saveSOLRXML($SOLRArray, $fileNameSOLR);
			
			if(file_exists($fileNameSOLR.'-SOLR.xml')) {
				echo $fileNameSOLR.'-SOLR.xml gespeichert.<br/>';
			}

			
			$facets = $catalogue->listFacets;
			$cloudFacets = $catalogue->cloudFacets;
			$doughnutFacets = $catalogue->doughnutFacets;
			
			/* Hier werden die Strukturen (jeweils ein Array aus section-Objekten) gebildet 
			und im Array $structures zwischengespeichert.
			*/
			$structures = array();
			include('fieldList.php');
			$count = 0;
			foreach($facets as $facet) {
				if(in_array($facet, $indexFields)) {
					$structure = makeSections($data, $facet);
					if(in_array($facet, $volumeFields)) {
						foreach($structure as $section) {
							$section = joinVolumes($section);
						}
					}
					$structures[] = $structure;
				}
			}
			
			// Zu jeder Struktur wird eine Liste mit Kategorien für das Inhaltsverzeichnis berechnet.
			$count = 0;
			$tocs = array();
			foreach($structures as $structure) {
				$tocs[$facets[$count]] = makeToC($structure);
				$count++;
			}
			
			if($tocs) {
				echo 'Inhaltsverzeichnisse erstellt.<br/>';
			}
			
			// Für jede Struktur wird jetzt eine HTML-Datei berechnet und gespeichert.
			$count = 0;
			
			foreach($structures as $structure) {
				$facet = $facets[$count];
				$navigation = makeNavigation($catalogue->fileName, $tocs, $facet);
				$content = makeHead($catalogue, $navigation, $facet);
				$content .= makeList($structure, $catalogue, $_SESSION['folderName']);
				$content .= $foot;
				$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-'.$facet.'.html';
				if($count == 0) {
					$firstFileName = $fileName;
				}
				$datei = fopen($fileName,"w");
				fwrite($datei, $content, 3000000);
				fclose($datei);
				$count++;
				if(file_exists($fileName)) {
					echo 'Datei '.$fileName.' wurde erstellt.<br/>';
				}
			}

			unset($structures);
						
			// Erzeugen der Seite mit den Word Clouds
			$navigation = makeNavigation($catalogue->fileName, $tocs, 'jqcloud');
			$content = makeHead($catalogue, $navigation, 'jqcloud');
			$content .= makeCloudPageContent($data, $catalogue->cloudFacets, $catalogue->fileName);
			$content .= $foot;
			$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-wordCloud.html';
			$datei = fopen($fileName,"w");
			fwrite($datei, $content, 3000000);
			fclose($datei);
			if(file_exists($fileName)) {
				echo 'Datei '.$fileName.' wurde erstellt.<br/>';
			}			

			// Erzeugen der Seite mit den Doughnut Charts
			$navigation = makeNavigation($catalogue->fileName, $tocs, 'doughnut');
			$content = makeHead($catalogue, $navigation, 'doughnut');
			$content .= makeDoughnutPageContent($data, $catalogue->doughnutFacets, $_SESSION['folderName']);
			$content .= $foot;
			$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-doughnut.html';
			$datei = fopen($fileName,"w");
			fwrite($datei, $content, 3000000);
			fclose($datei);
			if(file_exists($fileName)) {
				echo 'Datei '.$fileName.' wurde erstellt.<br/>
				';
			}
			
			zipFolderContent($_SESSION['folderName'], $catalogue->fileName);
			if(file_exists($_SESSION['folderName'].'/'.$catalogue->fileName.'.zip')) {
				echo 'Zip-Archiv '.$fileName.' wurde erstellt.<br/>
				';
			}
			
			
			echo '</p>';
			$_SESSION['catalogueObject'] = serialize($catalogue);
			echo '<p><a href="'.$firstFileName.'" target="_blank">Website aufrufen</a><br />
			<a href="'.$_SESSION['folderName'].'/'.$catalogue->fileName.'.zip">Download als ZIP-Datei</a></p>';
			
		}
		
function makeSelectForm() {
	include('fieldList.php');
	$result = '';
	$checkedFields = array('persName', 'year', 'placeName', 'subject');
	$checked = '';
	foreach($checkboxFields as $field) {
		if(in_array($field, $checkedFields)) {
			$checked = ' checked="checked"';
		}
		$boxes = '						<label class="checkbox-inline"><input type="checkbox" name="page#'.$field.'"'.$checked.'>Eigene Seite</label>';
		if(in_array($field, $wordCloudFields)) {
			$boxes .= '
									<label class="checkbox-inline"><input type="checkbox" name="cloud#'.$field.'"'.$checked.'>Wortwolke</label>';
		}
		if(in_array($field, $doughnutFields)) {
			$boxes .= '
									<label class="checkbox-inline"><input type="checkbox" name="doughnut#'.$field.'"'.$checked.'>Kreisdiagramm</label>';
		}
		$checked = '';
		$label = translateCheckboxNames($field);
		$result .= '
				<div class="form-group">
					<label class="control-label col-sm-3" for="'.$field.'">'.$label.' ('.$field.')</label>
					<div class="col-sm-9">
						'.$boxes.'
					</div>
				</div>';
	}
	return($result);
}

/* Receives Post-variables from form (makeSelectForm) and writes them to a catalogue object. */
function insertFacetsToCatalogue($catalogue, $post) {
	$catalogue->listFacets = array();
	$catalogue->cloudFacets = array();
	$catalogue->doughnutFacets = array();
	foreach($post as $facetString => $value) {
		$parts = explode('#', $facetString);
		if(isset($parts[1])) {
			$category = $parts[0];
			$facet = $parts[1];
			if($category == 'page') {
				$catalogue->listFacets[] = $facet;
			}
			elseif($category == 'cloud') {
				$catalogue->cloudFacets[] = $facet;
			}
			elseif($category == 'doughnut') {
				$catalogue->doughnutFacets[] = $facet;
			}
		}
	}
	return($catalogue);
}

function zipFolderContent($folder, $fileName) {
	$zip = new ZipArchive();
	$zipFile = $folder.'/'.$fileName.'.zip';
	if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
		die('cannot open '.$fileName);
	}	
	$options = array('add_path' => $fileName.'/', 'remove_all_path' => TRUE);
	$zip->addGlob($folder.'/*', 0, $options);
	$zip->close();
}
		
	?>
				
			</div>
		</body>
</html>