<?php

function current_url() {
	return $_SERVER['QUERY_STRING'];
}

function site_name() {
	global $lando;
	return $lando->config["site_name"];
}

function site_root() {
	global $lando;
	return $lando->config["site_root"];
}

function site_description() {
	global $lando;
	return $lando->config["site_description"];
}

function theme_dir() {
	global $lando;
	return "/themes/".$lando->config["theme"];
}