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
	function saveToFile() {
		$serialize = serialize($this);
		file_put_contents('geoDataArchive', $serialize);
	}
	function loadFromFile() {
		$archiveString = file_get_contents('geoDataArchive');
		$archive = unserialize($archiveString);
		unset($archiveString);
		$this->content = $archive->content;
	}
}

class geoDataArchiveEntry {
	public $label = 'kein Ortsname';
	public $lat;
	public $long;
	public $getty;
	public $geoNames;
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
		elseif((($this->lat == $otherEntry->lat) and ($this->long == $otherEntry->long)) and $this->lat != '') {
			$same = 1;
		}
		return($same);
	}
	
}

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
						$entry->long = $rowPlaceData['x'];
						$entry->lat = $rowPlaceData['y'];
						
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