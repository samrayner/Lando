<?php 

include "app/core/loader.php";

$path = current_url();
$thumb = isset($_GET["size"]) ? $_GET["size"] : false;

if($Lando->config["host"] == "dropbox" && strpos(strtolower($Lando->config["host_root"]), "/public") === 0) {
/*
	if(isset($Cache->account["uid"])) {	
		$url = "http://dl.dropbox.com/u/".encode_reslash($Cache->account["uid"]."/Lando/".$Lando->config["site_title"]."/$path");
		header("Location: $url");
	}
*/
}

$File = $Lando->get_file($path, $thumb);

if(!$File) {
	$custom_404 = trim_slashes($theme_dir)."/404.php";
	if(file_exists($custom_404))
		include $custom_404;
	else
		include "app/templates/404.php";
		
	exit();
}

header('Content-Type: '.$File->mime_type);

$source = $File->raw_content;

if($thumb)
	$source = base64_decode($source);

echo $source;

?>