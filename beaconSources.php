<?php

$beaconSources = array(
	'wkp' => array(
		'label' => 'Wikipedia',
		'location' => 'http://tools.wmflabs.org/persondata/beacon/dewiki.txt',
		'target' => 'http://tools.wmflabs.org/persondata/redirect/gnd/de/{ID}'
	),
	'db' => array(
		'label' => 'Deutsche Biographie',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacon_db_register.txt',
		'target' => 'http://www.deutsche-biographie.de/pnd{ID}.html'
	), 
	'cph' => array(
		'label' => 'Helmstedter Professorenkatalog',
		'location' => 'http://uni-helmstedt.hab.de/beacon.php',
		'target' => 'http://uni-helmstedt.hab.de/index.php?cPage=5&sPage=prof&wWidth=1920&wHeight=957&suche1=gnd&pnd1=&muster1={ID}'
	),		
	'cpr' => array(
		'label' => 'Rostocker Professorenkatalog',
		'location' => 'http://cpr.uni-rostock.de/cpr_pnd_beacon.txt',
		'target' => 'http://cpr.uni-rostock.de/pnd/{ID}'
	),		
	'cpl' => array(
		'label' => 'Leipziger Professorenkatalog',
		'location' => 'http://www.uni-leipzig.de/unigeschichte/professorenkatalog/leipzig/cpl-beacon.txt',
		'target' => 'http://www.uni-leipzig.de/unigeschichte/professorenkatalog/leipzig/pnd/{ID}'
	),		
	'cprm' => array(
		'label' => 'Matrikel der Universität Rostock',
		'location' => 'http://matrikel.uni-rostock.de/matrikel_rostock_pnd_beacon.txt',
		'target' => 'http://matrikel.uni-rostock.de/gnd/{ID}'
	),		
	'fruchtbringer' => array(
		'label' => 'Fruchtbringende Gesellschaft',
		'location' => 'http://www.die-fruchtbringende-gesellschaft.de/files/fg_beacon.txt',
		'target' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/fbges.php?pnd={ID}'
	),		
	'sandrart' => array(
		'label' => 'Sandrart.net',
		'location' => 'http://ta.sandrart.net/services/pnd-beacon/',
		'target' => 'http://ta.sandrart.net/services/pnd-beacon/?pnd={ID}'
	),		
	'dpi' => array(
		'label' => 'Digitaler Portraitindex',
		'location' => 'http://www.portraitindex.de/pnd_beacon.txt',
		'target' => 'http://www.portraitindex.de/dokumente/pnd/{ID}'
	),
	'vkk' => array(
		'label' => 'Virtuelles Kupferstichkabinett',
		'location' => 'http://www.virtuelles-kupferstichkabinett.de/beacon.php',
		'target' => 'http://www.virtuelles-kupferstichkabinett.de/index.php?reset=1&subPage=search&selTab=2&habFilter=1&haumFilter=1&selFilter=0&sKey1=pzusatz&sWord1={ID}'
	),		
	'trithemius' => array(
		'label' => 'Trithemius, De scriptoribus ecclesiasticis',
		'location' => 'http://www.mgh-bibliothek.de/beacon/trithemius',
		'target' => 'http://www.mgh.de/index.php?&wa72ci_url=%2Fcgi-bin%2Fmgh%2Fallegro.pl&db=opac&var5=IDN&TYP=&id=438&item5=trithemius_{ID}'
	),
	'fabricius' => array(
		'label' => 'Fabricius, Bibliotheca latina',
		'location' => 'http://www.mgh-bibliothek.de/beacon/fabricius',
		'target' => 'http://www.mgh.de/index.php?&wa72ci_url=%2Fcgi-bin%2Fmgh%2Fallegro.pl&db=opac&var5=IDN&TYP=&id=438&item5=fabricius_{ID}'
	)						
);

?>