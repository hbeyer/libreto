<?php

class geoDataArchive {
	
	public $date;
	public $content = array();
	
	function __construct() {
       $this->date = date("Y-m-d H:i:s");
	}
	
	function insertEntry($entry) {
		if($entry->lat != '' and $entry->long != '') {
			$this->content[] = $entry;
		}
	}
	
	function insertEntryIfNew($entry) {
		$check = 0;
		foreach($this->content as $oldEntry) {
			if($oldEntry->testIfSame($entry) == 1) {
				$check++;
			}
		}
		if($check == 0) {
			$this->insertEntry($entry);
		}
	}
	
	function insertGeoNamesArray($array) {
		foreach($array as $id) {
			$entry = makeEntryFromGeoNames($id);
			$this->insertEntryIfNew($entry);
		}
	}
	
	function saveToFile($fileName = 'geoDataArchive') {
		$serialize = serialize($this);
		file_put_contents($fileName, $serialize);
	}
	
	function loadFromFile($fileName = 'geoDataArchive') {
		$archiveString = file_get_contents($fileName);
		$archive = unserialize($archiveString);
		unset($archiveString);
		$this->content = $archive->content;
	}
	
	function getByGeoNames($id) {
		foreach($this->content as $entry) {
			if($entry->geoNames == $id) {
				return($entry);
				break;
			}
		}
	}
	
	function getByName($name) {
		foreach($this->content as $entry) {
			if($entry->label == $name) {
				return($entry);
				break;
			}
		}
	}
	
	function loadFromGeoBrowserCSV($fileName) {
		$csv = array_map('str_getcsv', file($fileName));
		$this->loadFromFile();
		$lastName = '';
		foreach($csv as $row) {
			if($row[0] != $lastName) {
				$entry = new geoDataArchiveEntry();
				$entry->label = $row[0];
				if($entry->label == '') {
					$entry->label = $row[1];
				}
				$entry->long =$row[3];
				$entry->lat = $row[4];
				$entry->getty = $row[8];
				$this->insertEntryIfNew($entry);
			}
			$lastName = $entry->label;
		}
		$this->saveToFile();
	}
	
	function makeEntryFromGeoNames($id) {
		$target = 'http://api.geonames.org/getJSON?formatted=true&geonameId='.$id.'&username=hbeyer';
		$responseString = file_get_contents($target);
		$response = json_decode($responseString);

		$varNames = array();
		foreach($response->alternateNames as $alternate) {
			if(isset($alternate->lang) and preg_match('~^de|la|fr|it|en$~', $alternate->lang) == 1) {
			$varNames[] = $alternate->name;
			}
		}
		$entry = new geoDataArchiveEntry();
		$entry->label = $response->toponymName;
		$entry->lat = $response->lat;
		$entry->long = $response->lng;
		$entry->geoNames = $id;
		$entry->altLabels = $varNames;
		return($entry);
	}
	
	function makeEntryFromGetty($id) {
		/* $target = 'http://api.geonames.org/getJSON?formatted=true&geonameId='.$id.'&username=hbeyer';
		$responseString = file_get_contents($target);
		$response = json_decode($responseString);

		$varNames = array();
		foreach($response->alternateNames as $alternate) {
			if(isset($alternate->lang) and preg_match('~^de|la|fr|it|en$~', $alternate->lang) == 1) {
			$varNames[] = $alternate->name;
			}
		}
		$entry = new geoDataArchiveEntry();
		$entry->label = $response->toponymName;
		$entry->lat = $response->lat;
		$entry->long = $response->lng;
		$entry->geoNames = $id;
		$entry->altLabels = $varNames;
		return($entry); */
	}	
	
	
	function makeEntryFromGND($gnd) {
		$target = 'http://d-nb.info/gnd/'.$gnd.'/about/lds';
		$response = file_get_contents($target);
		$RDF = new DOMDocument();
		$RDF->load($target);
			
		$nodePrefName = $RDF->getElementsByTagNameNS('http://d-nb.info/standards/elementset/gnd#', 'preferredNameForThePlaceOrGeographicName');
		$prefName = getTextContent($nodePrefName);
		
		$nodeVarName = $RDF->getElementsByTagNameNS('http://d-nb.info/standards/elementset/gnd#', 'variantNameForThePlaceOrGeographicName');
		$varNameString = getTextContent($nodeVarName);
		$varNameString = replaceArrowBrackets($varNameString);
		$varNames = explode('|', $varNameString);
		
		$nodeGeoData = $RDF->getElementsByTagNameNS('http://www.opengis.net/ont/geosparql#', 'asWKT');
		$geoDataString = getTextContent($nodeGeoData);
		preg_match('~ ([+-][0-9]{1,3}\.[0-9]{1,10}) ([+-][0-9]{1,3}\.[0-9]{1,10}) ~', $geoDataString, $matches);
		$long = '';
		$lat = '';
		if(isset($matches[1]) and isset ($matches[2])) {
			$long = $matches[1];
			$lat = $matches[2];
		}
		
		$nodeSameAs = $RDF->getElementsByTagNameNS('http://www.w3.org/2002/07/owl#', 'sameAs');
		$sameAs = getAttributeFromNodeList($nodeSameAs, 'rdf:resource');
		preg_match('~http://sws.geonames.org/([0-9]{5,10})~', $sameAs, $matches);
		$geoNames = '';
		if(isset($matches[1])) {
			$geoNames = $matches[1];
		}
		
		$entry = new geoDataArchiveEntry();
		$entry->label = replaceArrowBrackets($prefName);
		$entry->lat = $lat;
		$entry->long = $long;
		$entry->gnd = $gnd;
		$entry->geoNames = $geoNames;
		$entry->altLabels = $varNames;
		
		return($entry);
	}
	
}

class geoDataArchiveEntry {
	public $label = 'kein Ortsname';
	public $lat;
	public $long;
	public $getty;
	public $geoNames;
	public $gnd;
	public $altLabels = array();
	function addAltLabel($altLabel) {
		$this->altLabels[] = $altLabel;
	}
	
	function testIfSame($otherEntry) {
		$same = 0;
		if($this->label == $otherEntry->label) {
			$same = 1;
		}
		elseif($this->getty == $otherEntry->getty and $this->getty != '') {
			$same = 1;
		}
		elseif($this->geoNames == $otherEntry->geoNames and $this->geoNames != '') {
			$same = 1;
		}
		elseif($this->gnd == $otherEntry->gnd and $this->gnd != '') {
			$same = 1;
		}		
		elseif((($this->lat == $otherEntry->lat) and ($this->long == $otherEntry->long)) and $this->lat != '') {
			$same = 1;
		}
		return($same);
	}
}

// Nur für ein spezifisches Datenbankmodell geeignet, daher separat.
function load_from_mysql($database) {
	$dbGeo = new mysqli('localhost', 'root', '', $database);
	$dbGeo->set_charset("utf8");
		if (mysqli_connect_errno()) {
			die ('Konnte keine Verbindung zur Datenbank aufbauen: '.mysqli_connect_error().'('.mysqli_connect_errno().')');
		}
		
	$sql1 = 'SELECT distinct tgn FROM ort WHERE tgn IS NOT NULL';

	$archive = new geoDataArchive();
	$archive->loadFromFile();

	if($result = $dbGeo->query($sql1)) {
		$count = 1;
		while($rowPlaces = $result->fetch_assoc()) {
			$tgn = $rowPlaces['tgn'];
			$sql2 = 'SELECT * FROM ort WHERE tgn='.$tgn.'';
			if($result2 = $dbGeo->query($sql2)) {
				while($rowPlaceData = $result2->fetch_assoc()) {
					if(substr($rowPlaceData['ort'], 0, 1) != '[') {
						$count++;
						
						$entry = new geoDataArchiveEntry();
						$entry->label = $rowPlaceData['ort'];
						$entry->getty = strval($tgn);
						$entry->long = cleanCoordinate($rowPlaceData['x']);
						$entry->lat = cleanCoordinate($rowPlaceData['y']);
						
						$archive->insertEntryifNew($entry);
						
					}
				}
			}
		}
	}
	var_dump($archive);
	$archive->saveToFile();
	
}

?>