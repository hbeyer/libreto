<?php

function makeHead($thisCatalogue, $navigation, $field) {
	$fileName = fileNameTrans($thisCatalogue->heading);
	if($thisCatalogue->year) {
		$title = 	$thisCatalogue->heading.' ('.$thisCatalogue->year.')';
	}
	else {
		$title = 	$thisCatalogue->heading;
	}
	$transcriptionLink = '<br />
			<span id="switchLink"><a href="javascript:switchToOriginal()">Transkription des Katalogs</a></span><br/>&nbsp;</p>';
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
			<p>'.$thisCatalogue->title.$transcriptionLink.'
		</div>
		<nav class="navbar navbar-default" data-spy="affix" data-offset-top="197">'.$navigation.'
			<ul class="nav navbar-nav navbar-right" style="padding-right:15px">
				<li class="'.$classLi.'">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="glyphicon glyphicon-picture"></span> Visualisierungen<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="'.$fileName.'-wordCloud.html" title="Wortwolken"><span class="glyphicon glyphicon-cloud"></span> Word Clouds</a></li>
						<li><a href="'.$fileName.'-doughnut.html" title="Kreisdiagramme"><span class="glyphicon glyphicon-adjust"></span> Kreisdiagramme</a></li>				
						<li><a href="https://geobrowser.de.dariah.eu/?csv1=http://geobrowser.de.dariah.eu./storage/'.$thisCatalogue->GeoBrowserStorageID.'&currentStatus=mapChanged=Historical+Map+of+1650" target="_blank" title="Druckorte in Kartenansicht"><span class="glyphicon glyphicon-globe"></span> GeoBrowser</a></li>
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

?>