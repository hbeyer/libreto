<?php

function makeToC($structure) {
	$ToC = array();
	foreach($structure as $section) {
		if($section->level == 1) {
			$ToC[] = $section->label;
		}
	}
	return($ToC);
}	

function makeULContent($toc, $nameCat, $type) {
	$result = '';
	foreach($toc as $category) {
		$result .= '<li><a href="'.$nameCat.'-'.$type.'.html#'.translateAnchor($category).'">'.$category.'</a></li>';
	}
	return($result);
}


function makeNavigation($nameCat, $tocs, $type) {
	/*$tocs is an associative array of arrays created by the function makeToC,
	the index of which is the field the function makeIndex used to create the index categories
	$type is the field used for the actual page
	*/
	
	$nameCat = fileNameTrans($nameCat);
	
	$result = '<ul class="nav navbar-nav">';
	foreach($tocs as $field => $toc) {
		$classLiTop = 'download';
		if($field == $type) {
			$classLiTop = 'active';
		}
		$listItems = makeULContent($toc, $nameCat, $field);
		$fieldGer = translateFieldNames($field);
		$result .= '
					<li class="'.$classLiTop.'">
						<a class="dropdown-toggle" data-toggle="dropdown" href="'.$nameCat.'-'.$field.'.html">nach '.$fieldGer.'<span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="'.$nameCat.'-'.$field.'.html">Seitenanfang</a></li>
							'.$listItems.'
						</ul>
					</li>';
	}
	$result .= '</ul>';
	return($result);
}
	
?>