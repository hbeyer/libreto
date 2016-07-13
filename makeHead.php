<?php

function makeHead($thisCatalogue, $navigation, $field) {
	$fileName = fileNameTrans($thisCatalogue->fileName);
	if($thisCatalogue->year) {
		$title = $thisCatalogue->heading.' ('.$thisCatalogue->year.')';
	}
	else {
		$title = $thisCatalogue->heading;
	}
	
	$metadata = displayCatalogueMetadata($thisCatalogue, $field);
	
	$classLi = 'download';
	if($field == 'jqcloud' or $field == 'doughnut') {
		$classLi = 'active';
		$transcriptionLink = '';
	}
	$chart = '';
	if($field == 'doughnut') {
		$chart = '
		<script type="text/javascript" src="chart.js"></script>';
	}
	$cloudEntry = '';
	if($thisCatalogue->cloudFacets != array()) {
		$cloudEntry = '<li><a href="'.$fileName.'-wordCloud.html" title="Wortwolken">Word Clouds</a></li>';
	}
	$doughnutEntry = '';
	if($thisCatalogue->doughnutFacets != array()) {
		$doughnutEntry = '<li><a href="'.$fileName.'-doughnut.html" title="Kreisdiagramme">Kreisdiagramme</a></li>';
	}
	
	$geoBrowserLink = makeGeoBrowserLink($thisCatalogue->GeoBrowserStorageID, $thisCatalogue->year);
	
	$frontMatter = '<!DOCTYPE html>
<html lang="en">
	<head>
		<title>'.$thisCatalogue->heading.'</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="proprietary.js"></script>'.$chart.'
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="proprietary.css">
	</head>
	<body onload="javascript:x = getNavBarHeight();scrollNav(x);">
		<div class="container">
		<div class="container-fluid">
			<h1>'.$title.'</h1>
			'.$metadata.'
		</div>
		<nav class="navbar navbar-default" data-spy="affix" data-offset-top="197">'.$navigation.'
			<ul class="nav navbar-nav navbar-right" style="padding-right:15px">
				<li class="'.$classLi.'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-picture"></span> Visualisierung<span class="caret"></span></a>
					<ul class="dropdown-menu">
						'.$cloudEntry.'
						'.$doughnutEntry.'				
						<li><a href="'.$geoBrowserLink.'" target="_blank" title="Druckorte in Kartenansicht">GeoBrowser</a></li>
					</ul>
				</li>
			</ul>
		</nav>
		<div class="container-fluid">';
		return($frontMatter);
}

$foot = '
			</div>
		</div>
	</body>
</html>';

function displayCatalogueMetadata($catalogue, $field) {
	$return = '<p>';
	if($catalogue->description) {
		$return .= $catalogue->description.'</p><p>';
	}
	if($catalogue->institution and $catalogue->shelfmark) {
		$return .= 'Altkatalog: '.$catalogue->institution.', '.$catalogue->shelfmark;
	}
	if($catalogue->title and $catalogue->description == '') {
		$title = $catalogue->title;
		$return .= ': '.$title;
	}
	if($catalogue->base) {
		$space = ' ';
		if($return == '<p>') {
			$space = '';
		}
		$return .= $space.'<a href="'.$catalogue->base.'1" target="_blank">[Digitalisat]</a>';
	}
	if($catalogue->shelfmark and $field != 'doughnut' and $field != 'wordCloud') {
		$return .= '<br /><span id="switchLink"><a href="javascript:switchToOriginal()">Anzeige in Vorlageform</a></span>';
	}
	$return .= '</p>';
	return($return);
}

function makeGeoBrowserLink($storageID, $year) {
	$mapDate = assignMapDate($year);
	$link = 'https://geobrowser.de.dariah.eu/?csv1=http://geobrowser.de.dariah.eu./storage/'.$storageID.'&currentStatus=mapChanged=Historical+map+of+'.$mapDate;
	return($link);
}

function assignMapDate($year) {
	$historicalMaps = array(400, 600, 800, 1000, 1279, 1492, 1530, 1650, 1715, 1783, 1815, 1880, 1914, 1920, 1938, 1949, 1994, 2006);
	$year = intval($year);
	$selectedYear = 400;
	$diversionOld = 10000;
	foreach($historicalMaps as $mapDate) {
		$diversion = abs($mapDate - $year);
		if($diversion < $diversionOld) {
			$selectedYear = $mapDate;
		}
		$diversionOld = $diversion;
	}
	return($selectedYear);
}

?>