<?php
include('classDefinition.php');
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Rekonstruktion historischer Bibliotheken</title>
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
			<?php
			if(isset($_POST['heading'])) {
				$postedData = array();
				 foreach($_POST as $field => $content) {
					$postedData[$field] = htmlspecialchars($content);
				}
				$facetsInForm = array('persons', 'year', 'publisher', 'place', 'subject', 'genre', 'language', 'format');
				$facets = array('cat');
				foreach($facetsInForm as $facet) {
					if(isset($postedData[$facet])) {
						$facets[] = $facet;
					}
				}
				$thisCatalogue = new catalogue();
				$thisCatalogue->database = $postedData['database'];
				$thisCatalogue->key = $thisCatalogue->database;
				$thisCatalogue->heading = $postedData['heading'];
				$thisCatalogue->abstr = $postedData['abstract'];
				$thisCatalogue->title = $postedData['title'];
				$thisCatalogue->printer = $postedData['cataloguePrinter'];
				$thisCatalogue->year = $postedData['yearCatalogue'];
				$thisCatalogue->base = $postedData['base'];
				$thisCatalogue->copy['institution'] = $postedData['institution'];
				$thisCatalogue->copy['shelfmark'] = $postedData['shelfmark'];
				
			}
			?>
				<h1>Tool zur Rekonstruktion historischer Bibliotheken<br />
				***WORK IN PROGRESS***</h1>
				<!-- <h2>Datenanreicherung</h2>
				<form class="form-inline" role="form">
				<label for="databaseBEACON"></label>
				<input type="text" class="form-control" id="databaseBEACON">&nbsp;<button type="submit" class="btn btn-default">Submit</button>
				</form>
				<h2>Erzeugen von HTML-Dateien</h2> -->
				<form role="form" action="index.php" method="post">
					<h3>Daten zum Projekt</h3>
					<div class="form-group">
						<label for="heading">Titel des Projekts</label>
						<input type="text" class="form-control" name="heading" maxlength="40" required="required">			
					</div>
					<div class="form-group">
						<label for="titel">Projektbeschreibung</label>
						<input type="text" class="form-control" name="abstract" maxlength="1500">			
					</div>		
					<div class="form-group">
						<label for="database">Datenbank</label>
						<input type="text" class="form-control" name="database" maxlength="25" required="required">							
					</div>									
					<!-- <div class="form-group">
						<label for="key">Dateinamenerweiterung</label>
						<input type="text" class="form-control" name="key" maxlength="6">			
					</div>	 -->
					<h3>Daten zum Altkatalog</h3>
					<div class="form-group">
						<label for="title">Titel</label>
						<input type="text" class="form-control" name="title" maxlength="1500">
					</div>
					<div class="form-group">				
						<label for="cataloguePrinter">Drucker</label>
						<input type="text" class="form-control" name="cataloguePrinter" maxlength="40">
					</div>
					<div class="form-group">				
						<label for="catalogueYear">Erscheinungsjahr</label>
						<input type="yearCatalogue" class="form-control" name="yearCatalogue" maxlength="4">
					</div>
					<div class="form-group">				
						<label for="institution">Besitzende Institution</label>
						<input type="text" class="form-control" name="institution" maxlength="150">
					</div>
					<div class="form-group">				
						<label for="shelfmark">Signatur</label>
						<input type="text" class="form-control" name="shelfmark" maxlength="40">
					</div>					
					<div class="form-group">
						<label for="base">Basis-URL</label>
						<input type="url" class="form-control" name="base" maxlength="150">						
					</div>
					<h3>Gewünschte Facetten</h3>
					<div class="checkbox">
						<label><input type="checkbox" name="persons"> Personen</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="year"> Erscheinungsjahr</label> 
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="publisher"> Drucker</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="place"> Verlagsort</label>
					</div>
					<div class="checkbox">						
						<label><input type="checkbox" name="subject"> Inhalt</label> 
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="genre"> Gattung</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="language"> Sprachen</label>
					</div>
					<div class="checkbox">
						<label><input type="checkbox" name="format"> Materialart</label>
					</div>
					<hr>
					<div class="form-group">		
						<button type="submit" class="btn btn-default">Abschicken</button>
					</div>
				</form>	
			</div>
		</body>
</html>