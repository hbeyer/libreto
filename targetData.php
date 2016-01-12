<?php
//Bei den Indices werden Ä, Ö, Ü, ä, ö, ü und ß durch Umschrift (ae, oe, ue, ss) ersetzt, Leerzeichen entfernt und Groß- in Kleinbuchstaben konvertiert.

$bases = array(
	'vd16' => 'http://gateway-bayern.de/VD16+{ID}', 
	'vd17' => 'http://gso.gbv.de/DB=1.28/CMD?ACT=SRCHA&IKT=8002&TRM={ID}', 
	'vd18' => 'http://vd18.de/id/{ID}', 
	'edit16' => 'http://edit16.iccu.sbn.it/scripts/iccu_ext.dll?fn=10&i={ID}',
	'edit' => 'http://edit16.iccu.sbn.it/scripts/iccu_ext.dll?fn=10&i={ID}',
	'swissbib' => 'https://www.swissbib.ch/Record/{ID}',
	'gw' => 'http://gesamtkatalogderwiegendrucke.de/docs/GW{ID}.htm',
	'rero' => 'http://data.rero.ch/01-{ID}/html?view=RERO_V1&lang=de',
	'stcn' => 'http://picarta.pica.nl/DB=3.11/XMLPRS=Y/PPN?PPN={ID}',
	'gbv' => 'http://gso.gbv.de/DB=2.1/PPNSET?PPN={ID}',
	'swb' => 'http://swb.bsz-bw.de/DB=2.1/PPNSET?PPN={ID}',
	'parisbnf' => 'http://catalogue.bnf.fr/ark:/12148/cb{ID}/PUBLIC',
	'inka' => 'http://www.inka.uni-tuebingen.de/?inka={ID}',
	'bvb' => 'http://gateway-bayern.de/{ID}',
	'hbz' => 'http://lobid.org/resource/{ID}',
	'londonbl' => 'http://explore.bl.uk/primo_library/libweb/action/display.do?doc={ID}',
	'denhaagkb' => 'http://opc4.kb.nl/DB=1/PPNSET?PPN={ID}',
	'copac' => 'http://copac.jisc.ac.uk/id/{ID}?style=html',
	'sudoc' => 'http://www.sudoc.fr/{ID}',
	'unicat' => 'http://www.unicat.be/uniCat?func=search&query=sysid:{ID}',
	'sbb' => 'http://stabikat.de/DB=1/PPNSET?PPN={ID}',
	'lbvoe' => 'http://lb1.dabis.org/PSI/redirect.psi&sessid=---&strsearch=IDU={ID}',
	'uantwerpen' => 'http://anet.be/record/opacuantwerpen/{ID}',
	'josiah' => 'http://josiah.brown.edu/record={ID}',
	'solo' => 'http://solo.bodleian.ox.ac.uk/OXVU1:oxfaleph{ID}',
	'uul' => 'http://aleph.library.uu.nl/F?func=direct&doc_number={ID}',
	'nebis' => 'http://opac.nebis.ch/F?func=direct&local_base=NEBIS&doc_number={ID}',
	'buva' => 'http://permalink.opc.uva.nl/item/{ID}'
	);
	
$patternSystems = array(
	'vd16' => '~VD[ ]?16 ([A-Z][A-Z]? [0-9]{1,5})~', 
	'vd17' => '~VD[ ]?17 ([0-9]{1,3}:[0-9]{1,7}[A-Z])~', 
	//'vd18' => '', 
	//'edit16' => '',
	//'edit' => '',
	//'swissbib' => '',
	'gw' => '~GW[ ]?([0-9]{5,9})~', //Nummer müssen für das Retrieval auf 5 oder 6 Stellen aufgerundet werden.
	//'rero' => '',
	'stcn' => '~STCN[ ]?([0-9]{8}[0-9X])~',
	'gbv' => '~PPN ([0-9]{9}) |GBV ([0-9]{9})~',
	//'swb' => '',
	//'parisbnf' => '',
	//'inka' => '',
	//'bvb' => '',
	//'hbz' => '',
	//'londonbl' => '',
	//'denhaagkb' => '',
	//'copac' => '',
	//'sudoc' => '',
	//'unicat' => '',
	//'sbb' => '',
	//'lbvoe' => '',
	//'uantwerpen' => '',
	//'josiah' => '',
	//'solo' => '',
	//'uul' => '',
	//'nebis' => '',
	'buva' => '~BUvA ([0-9]+)~'
	);
	
?>