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
	'dbi' => array(
		'label' => 'Dizionario Biografico degli Italiani',
		'location' => 'http://beacon.findbuch.de/downloads/patchwork/pw_dbi-gndbeacon.txt',
		'target' => 'http://beacon.findbuch.de/gnd-resolver/pw_dbi/{ID}'
	),
	'hls' => array(
		'label' => 'Historisches Lexikon der Schweiz',
		'location' => 'http://beacon.findbuch.de/downloads/hls/hls-pndbeacon.txt',
		'target' => 'http://beacon.findbuch.de/pnd-resolver/hls/{ID}'
	),		
	'blko' => array(
		'label' => 'Biographisches Lexikon des Kaiserthums Oesterreich',
		'location' => 'http://tools.wmflabs.org/persondata/beacon/dewikisource_blkoe.txt',
		'target' => 'http://tools.wmflabs.org/persondata/redirect/gnd/ws-blkoe/{ID}'
	),
	'pbbl' => array(
		'label' => 'Personen in bayrischen historischen biographischen Lexika',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/bsb_personen.php?beacon',
		'target' => 'http://personen.digitale-sammlungen.de/pnd/treffer.html?object=liste&suche=pndid:{ID}%20AND%20(bsbID:bsb00000273%20OR%20bsbID:bsb00000274%20OR%20bsbID:bsb00000279%20OR%20bsbID:bsb00000280%20OR%20bsbID:bsb00000281%20OR%20bsbID:bsb00000282%20OR%20bsbID:bsb00000283%20OR%20bsbID:bsb00000284)&pos=1'
	),
	'trithemius' => array(
		'label' => 'Trithemius: De scriptoribus ecclesiasticis',
		'location' => 'http://www.mgh-bibliothek.de/beacon/trithemius',
		'target' => 'http://www.mgh.de/index.php?&wa72ci_url=%2Fcgi-bin%2Fmgh%2Fallegro.pl&db=opac&var5=IDN&TYP=&id=438&item5=trithemius_{ID}'
	),
	'fabricius' => array(
		'label' => 'Fabricius: Bibliotheca latina',
		'location' => 'http://www.mgh-bibliothek.de/beacon/fabricius',
		'target' => 'http://www.mgh.de/index.php?&wa72ci_url=%2Fcgi-bin%2Fmgh%2Fallegro.pl&db=opac&var5=IDN&TYP=&id=438&item5=fabricius_{ID}'
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
	'cpm' => array(
		'label' => 'Catalogus Professorum der Universität Mainz',
		'location' => 'http://gutenberg-biographics.ub.uni-mainz.de/gnd/personen/beacon/file.txt',
		'target' => 'http://gutenberg-biographics.ub.uni-mainz.de/gnd/{ID}'
	),
	'cprm' => array(
		'label' => 'Matrikel der Universität Rostock',
		'location' => 'http://matrikel.uni-rostock.de/matrikel_rostock_pnd_beacon.txt',
		'target' => 'http://matrikel.uni-rostock.de/gnd/{ID}'
	),
	'hvuz' => array(
		'label' => 'Historische Vorlesungsverzeichnisse der Universität Zürich 1833–1900',
		'location' => 'http://histvv.uzh.ch/pnd.txt',
		'target' => 'http://histvv.uzh.ch/pnd/{ID}'
	),
	'mabk' => array(
		'label' => 'Matrikel der Akademie der Bildenden Künste München',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/adbk.php?beacon',
		'target' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/adbk.php?pnd={ID}'
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
	'kall' => array(
		'label' => 'Kalliope Verbundkatalog',
		'location' => 'http://kalliope.staatsbibliothek-berlin.de/beacon/beacon.txt',
		'target' => 'http://kalliope.staatsbibliothek-berlin.de/de/eac?eac.id={ID}'
	),	
	'zdn' => array(
		'label' => 'Zentrale Datenbank Nachlässe',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/zdn.php?beacon',
		'target' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/zdn.php?pnd={ID}'
	), 
	'sf2' => array(
		'label' => 'Schatullrechnungen Friedrichs des Großen',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/friedrich_schatullrechnungen.php?beacon',
		'target' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/friedrich_schatullrechnungen.php?pnd={ID}'
	),	
	'dta' => array(
		'label' => 'Deutsches Textarchiv',
		'location' => 'http://www.deutschestextarchiv.de/api/beacon',
		'target' => 'http://www.deutschestextarchiv.de/api/pnd/{ID}'
	),
	'cors' => array(
		'label' => 'correspSearch – Verzeichnisse von Briefeditionen',
		'location' => 'http://correspsearch.net/api/v1/gnd-beacon.xql?correspondent=all',
		'target' => 'http://correspsearch.bbaw.de/search.xql?correspondent=http://d-nb.info/gnd/{ID}&l=de'
	),
	'muenz' => array(
		'label' => 'Interaktiver Katalog des Münzkabinetts Staatliche Museen zu Berlin – Stiftung Preußischer Kulturbesitz',
		'location' => 'http://ww2.smb.museum/ikmk/beacon_gnd.php',
		'target' => 'http://ww2.smb.museum/ikmk/filter_text.php?filter%5B0%5D%5Bfield%5D=gnd&filter%5B0%5D%5Btext%5D={ID}'
	),	
	'dpi' => array(
		'label' => 'Digitaler Portraitindex',
		'location' => 'http://www.portraitindex.de/pnd_beacon.txt',
		'target' => 'http://www.portraitindex.de/dokumente/pnd/{ID}'
	),
/* 	'tpd' => array(
		'label' => 'Tripota - Trierer Porträtdatenbank',
		'location' => 'http://www.tripota.uni-trier.de/beacon_tripota.txt',
		'target' => 'http://www.tripota.uni-trier.de/beacon.php?ID={ID}'
	),
	'puk' => array(
		'label' => 'Portraitsammlung der USB Köln',
		'location' => 'http://beacon.findbuch.de/downloads/ps_usbk/DE-38-USB_Koeln-Portraitsammlung-portraitierte-beacon.txt',
		'target' => 'http://beacon.findbuch.de/portraits/ps_usbk?format=redirect&id={ID}'
	),	 */
 	'vkk' => array(
		'label' => 'Virtuelles Kupferstichkabinett',
		'location' => 'http://www.virtuelles-kupferstichkabinett.de/beacon.php',
		'target' => 'http://www.virtuelles-kupferstichkabinett.de/index.php?reset=1&subPage=search&selTab=2&habFilter=1&haumFilter=1&selFilter=0&sKey1=pzusatz&sWord1={ID}'
	),
	'imslp' => array(
		'label' => 'International Music Score Library Project',
		'location' => 'http://beacon.findbuch.de/downloads/patchwork/pw_imslp-gndbeacon.txt',
		'target' => 'http://beacon.findbuch.de/gnd-resolver/pw_imslp/{ID}'
	),
	'cmvw' => array(
		'label' => 'Carl Maria von Weber Gesamtausgabe (WeGA)',
		'location' => 'http://weber-gesamtausgabe.de/pnd_beacon.txt',
		'target' => 'http://www.weber-gesamtausgabe.de/de/pnd/{ID}'
	),
	'cfgb' => array(
		'label' => 'Carl Friedrich Gauss Briefwechsel',
		'location' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/gauss.php?beacon',
		'target' => 'http://www.historische-kommission-muenchen-editionen.de/beacond/gauss.php?pnd={ID}'
	)	
);

$beaconKeys = array();
foreach($beaconSources as $key => $value) {
	$beaconKeys[] = $key;
}

?>