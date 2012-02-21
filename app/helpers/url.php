<?php

function current_path() {
	$path_info = isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], "/") : "";
	return strtolower("/$path_info");
}

function path_segments($path=null) {
	global $Lando;

	if(!$path)
		$path = current_path();

	$segs = explode("/", $path);
	
	if(!$Lando->config["pretty_urls"])
		$segs[0] = "index.php";
	
	return $segs;
}

function path_segment($n) {
	$segs = path_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}

function current_url() {
	return implode("/", url_segments());
}

function url_segments() {
	$segs[0] = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	
	if($_SERVER["SERVER_PORT"] != "80")
    $segs[0] .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	else 
    $segs[0] .= $_SERVER["SERVER_NAME"];
    
  $path = trim(str_replace("?".$_SERVER["QUERY_STRING"], "", $_SERVER["REQUEST_URI"]), "/");
   
  return array_merge($segs, explode("/", $path));
}

function url_segment($n) {
	$segs = url_segments();
	return isset($segs[$n]) ? $segs[$n] : false;
}

function guess_site_root() {
	return preg_replace('~(/admin|/install)?/?(.*\.php)?(/index\.php)?/?'.trim_slashes(preg_quote(current_path())).'$~', "", current_url());
}