<?php
//Bei den Indices werden Ä, Ö, Ü, ä, ö, ü und ß durch Umschrift (ae, oe, ue, ss) ersetzt, Leerzeichen entfernt und Groß- in Kleinbuchstaben konvertiert.
$bases = array(
	'vd16' => 'http://gateway-bayern.de/VD16+', 
	'vd17' => 'http://gso.gbv.de/DB=1.28/CMD?ACT=SRCHA&IKT=8002&TRM=', 
	'vd18' => 'http://vd18.de/id/', 
	'edit16' => 'http://edit16.iccu.sbn.it/scripts/iccu_ext.dll?fn=10&i=',
	'edit' => 'http://edit16.iccu.sbn.it/scripts/iccu_ext.dll?fn=10&i=',
	'swissbib' => 'https://www.swissbib.ch/Record/',
	'gw' => 'http://gesamtkatalogderwiegendrucke.de/docs/GW',
	'rero' => 'http://data.rero.ch/01-',
	'stcn' => 'http://picarta.pica.nl/DB=3.11/XMLPRS=Y/PPN?PPN=',
	'gbv' => 'http://gso.gbv.de/DB=2.1/PPNSET?PPN=',
	'swb' => 'http://swb.bsz-bw.de/DB=2.1/PPNSET?PPN=',
	'parisbnf' => 'http://catalogue.bnf.fr/ark:/12148/cb',
	'inka' => 'http://www.inka.uni-tuebingen.de/?inka=',
	'bvb' => 'http://gateway-bayern.de/',
	'hbz' => 'http://lobid.org/resource/',
	'londonbl' => 'http://explore.bl.uk/primo_library/libweb/action/display.do?doc=',
	'denhaagkb' => 'http://opc4.kb.nl/DB=1/PPNSET?PPN=',
	'copac' => 'http://copac.jisc.ac.uk/id/',
	'sudoc' => 'http://www.sudoc.fr/',
	'unicat' => 'http://www.unicat.be/uniCat?func=search&query=sysid:',
	'sbb' => 'http://stabikat.de/DB=1/PPNSET?PPN=',
	'lbvoe' => 'http://lb1.dabis.org/PSI/redirect.psi&sessid=---&strsearch=IDU=',
	'uantwerpen' => 'http://anet.be/record/opacuantwerpen/',
	'josiah' => 'http://josiah.brown.edu/record=',
	'solo' => 'http://solo.bodleian.ox.ac.uk/OXVU1:oxfaleph',
	'uul' => 'http://aleph.library.uu.nl/F?func=direct&doc_number=',
	'nebis' => 'http://opac.nebis.ch/F?func=direct&local_base=NEBIS&doc_number=',
	'buva' => 'http://permalink.opc.uva.nl/item/'
	);
$endings = array(
	'gw' => '.htm',
	'rero' => '/html?view=RERO_V1&lang=de',
	'copac' => '?style=html',
	'parisbnf' => '/PUBLIC'
	);
?>