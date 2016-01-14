<?php

function compareCatalogue($a, $b) {
	if($a->id == $b->id) {
		return 0;
	}
	else {
		return ($a->id < $b->id) ? -1 : 1;
	}
}

function compareSubject($a, $b) {
	if($a->subject == $b->subject) {
		return 0;
	}
	else {
		return ($a->subject < $b->subject) ? -1 : 1;
	}
}

function languageIndex($a, $b) {
	if($a->label == $b->label) {
		return 0;
	}
	else {
		return ($a->label < $b->label) ? -1 : 1;
	}
}

?>
