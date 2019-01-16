<?php
include('classDefinition.php');
include('settings.php');
include('makeXML.php');
include('addToSolr.php');
include('encode.php');
include('auxiliaryFunctions.php');
include('makeIndex.php');
include('makeSection.php');
include('makeNavigation.php');
include('makePage.php');
include('makeEntry.php');
include('makeCloudList.php');
include('makeDoughnutList.php');
include('class_reference.php');
include('class_beacon_repository.php');
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
			
			saveXML($data, $catalogue, $_SESSION['folderName']);
			if(file_exists($_SESSION['folderName'].'/'.$catalogue->fileName.'.xml')) {
				echo $_SESSION['folderName'].'/'.$catalogue->fileName.'.xml erstellt.<br/>';
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
				$navigation = makeNavigation($catalogue, $tocs, $facet);
                $pageContent = makeList($structure, $catalogue);
                $content = makePage($catalogue, $navigation, $pageContent, $facet);
				$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-'.$facet.'.html';
				if($count == 0) {
					$firstFileName = $fileName;
				}
				$datei = fopen($fileName,"w");
				fwrite($datei, $content, 10000000);
				fclose($datei);
				$count++;
				if(file_exists($fileName)) {
					echo 'Datei '.$fileName.' wurde erstellt.<br/>';
				}
			}
			
			unset($structures);
			
			//Anlegen der Datei index.php mit Weiterleitung auf die Startseite
			makeStartPage($facets[0], $catalogue->fileName, $_SESSION['folderName']);
						
			// Erzeugen der Seite mit den Word Clouds
			if($catalogue->cloudFacets != array()) {
				$navigation = makeNavigation($catalogue, $tocs, 'jqcloud');
                $pageContent = makeCloudPageContent($data, $catalogue->cloudFacets, $catalogue->fileName);
                $content = makePage($catalogue, $navigation, $pageContent, 'jqcloud');
				$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-wordCloud.html';
				$datei = fopen($fileName,"w");
				fwrite($datei, $content, 10000000);
				fclose($datei);
				if(file_exists($fileName)) {
					echo 'Datei '.$fileName.' wurde erstellt.<br/>';
				}
			}

			// Erzeugen der Seite mit den Doughnut Charts
			if($catalogue->doughnutFacets != array()) {
				$navigation = makeNavigation($catalogue, $tocs, 'doughnut');
                $pageContent = makeDoughnutPageContent($data, $catalogue->doughnutFacets, $_SESSION['folderName']);
                $content = makePage($catalogue, $navigation, $pageContent, 'doughnut');
				$fileName = fileNameTrans($_SESSION['folderName'].'/'.$catalogue->fileName).'-doughnut.html';
				$datei = fopen($fileName,"w");
				fwrite($datei, $content, 10000000);
				fclose($datei);
				if(file_exists($fileName)) {
					echo 'Datei '.$fileName.' wurde erstellt.<br/>
					';
				}
			}
			
			zipFolderContent($_SESSION['folderName'], $catalogue->fileName);
			if(file_exists($_SESSION['folderName'].'/'.$catalogue->fileName.'.zip')) {
				echo 'Zip-Archiv '.$_SESSION['folderName'].'/'.$catalogue->fileName.'.zip wurde erstellt.<br/>';
			}
			
			
			echo '</p>';
			echo '<p><a href="'.$firstFileName.'" target="_blank">Website aufrufen</a><br />
			<a href="'.$_SESSION['folderName'].'/'.$catalogue->fileName.'.zip">Download als ZIP-Datei</a></p>';
			$_SESSION['catalogueObject'] = serialize($catalogue);
		}
		
function makeSelectForm() {
	include('fieldList.php');
	$result = '';
	$checkedFields = array('persName', 'year', 'placeName', 'subjects', 'genres', 'languages', 'publisher');
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
	$zip = new ZipArchive;
	$zipFile = $folder.'/'.$fileName.'.zip';
	if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
		die('cannot open '.$fileName);
	}	
	$options = array('add_path' => $fileName.'/', 'remove_all_path' => TRUE);
	$zip->addGlob($folder.'/*.html', 0, $options);
	$zip->addGlob($folder.'/*.x*', 0, $options);
	$zip->addGlob($folder.'/*.js', 0, $options);
	$zip->addGlob($folder.'/*.php', 0, $options);
	$zip->addGlob($folder.'/*.c*', 0, $options);
	$zip->addGlob($folder.'/*.ttl', 0, $options);
	$zip->addGlob($folder.'/*.rdf', 0, $options);
	$zip->addGlob($folder.'/*.kml', 0, $options);
	$zip->addFile($folder.'/dataPHP', $fileName.'/dataPHP');

	$zip->close();
}

function makeStartPage($facet, $fileName, $folder) {
	$content = '<?php
header("Location: '.$fileName.'-'.$facet.'.html");
?>';
	$datei = fopen($folder.'/index.php',"w");
	fwrite($datei, $content, 1000000);
	fclose($datei);
	if(file_exists($folder.'/index.php')) {
		echo 'Datei '.$folder.'/index.php wurde erstellt.<br/>';
	}
}

		
	?>
				
			</div>
		</body>
</html>
