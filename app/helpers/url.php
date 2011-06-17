<?php

function current_url() {
	return "/".trim_slashes($_SERVER['QUERY_STRING']);
}

function url_segments() {
	$segs = explode("/", current_url());
	$segs[0] = site_root();
	return $segs;
}

function url_segment($n) {
	$segs = url_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}