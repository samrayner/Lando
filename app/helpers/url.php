<?php

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

function encode_reslash($url) {
	return str_replace("%2F", "/", rawurlencode($url));
}