<?php

function site_title() {
	global $lando;
	return $lando->config["site_title"];
}

function site_root() {
	global $lando;
	$root = $lando->config["site_root"];
	if(!$lando->config["pretty_urls"])
		$root .= "/?";
	return $root;
}

function site_description() {
	global $lando;
	return $lando->config["site_description"];
}

function theme_dir() {
	global $lando;
	return "/themes/".$lando->config["theme"];
}

function cloud_account_info() {
	global $lando;
	return $lando->get_host_account_info();
}