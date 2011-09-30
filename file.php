<?php 

include "app/core/loader.php";

$path = current_url();
$thumb = isset($_GET["size"]) ? $_GET["size"] : false;

if(!$thumb && $Lando->config["host"] == "dropbox" && preg_match('~^/public~i', $Lando->config["host_root"])) {

	$account = $Lando->get_host_info();

	if(isset($account["uid"])) {	
		$full_path = $account["uid"].preg_replace('~^/public~i', "", $Lando->config["host_root"]).$path;
		$url = "http://dl.dropbox.com/u/".str_replace("%2F", "/", rawurlencode($full_path));
		
		header("Location: $url");
		exit();
	}
}

$File = $Lando->get_file($path, $thumb);

if(!$File) {
	$custom_404 = trim_slashes($theme_dir)."/404.php";
	if(include_exists($custom_404))
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