<?php

function makeHead($heading, $year, $abstract, $navigation, $GeoBrowserStorageID) {
	$fileName = fileNameTrans($heading);
	if($year) {
		$title = 	$heading.' ('.$year.')';
	}
	else {
		$title = 	$heading;
	}
	$frontMatter = '<!DOCTYPE html>
<html lang="en">
	<head>
		<title>'.$heading.'</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script type="text/javascript" src="jsfunctions.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="proprietary.css">
		<script type="text/javascript">
			window.addEventListener("load", function() { scrollBy(0, -65) })		
			window.addEventListener("hashchange", function() { scrollBy(0, -65) })
		</script>
	</head>
	<body>
		<div class="container">
		<div class="container-fluid">
			<h1>'.$title.'</h1>
			<p>'.$abstract.'<br />
			<span id="switchLink"><a href="javascript:switchToOriginal()">Transkription des Katalogs</a></span><br/>&nbsp;</p>
			<!-- <div id="button" style="text-align:left"><button class="btn btn-default" onclick="switchToOriginal()">Originaltitel</button></div> -->
		</div>
		<nav class="navbar navbar-default" data-spy="affix" data-offset-top="197">'.$navigation.'
			<ul class="nav navbar-nav navbar-right" style="padding-right:15px">
				<li><a href="https://geobrowser.de.dariah.eu/?csv1=http://geobrowser.de.dariah.eu./storage/'.$GeoBrowserStorageID.'&currentStatus=mapChanged=Historical+Map+of+1650" target="_blank" title="Druckorte in Kartenansicht"><span class="glyphicon glyphicon-globe"></span> GeoBrowser</a></li>
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