<?php

function site_title() {
	global $Lando;
	return $Lando->config["site_title"];
}

function site_root() {
	global $Lando;
	$root = $Lando->config["site_root"];
	if(!$Lando->config["pretty_urls"])
		$root .= "/?";
	return $root;
}

function site_description() {
	global $Lando;
	return $Lando->config["site_description"];
}

function theme_dir() {
	global $Lando;
	return "/themes/".$Lando->config["theme"];
}