<?php

function pages($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("pages"), $limit, $offset, $year, $month, $day);
}

function posts($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("posts"), $limit, $offset, $year, $month, $day);
}

function drafts($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("drafts"), $limit, $offset, $year, $month, $day);
}

function snippet($title) {
	global $Lando;
	return $Lando->get_content("snippets", $title);
}