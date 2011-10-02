<?php

function request_url() {
	$page_url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	if ($_SERVER["SERVER_PORT"] != "80")
	    $page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	else 
	    $page_url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	return $page_url;
}

function current_url() {
	$path_info = isset($_SERVER['PATH_INFO']) ? trim_slashes($_SERVER['PATH_INFO']) : "";
	return strtolower("/$path_info");
}

function url_segments() {
	global $site_root;

	$segs = explode("/", current_url());
	$segs[0] = $site_root;
	return $segs;
}

function url_segment($n) {
	$segs = url_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}