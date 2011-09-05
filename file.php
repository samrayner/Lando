<?php 

include "app/core/loader.php";

$path = current_url();
$thumb = isset($_GET["size"]) ? $_GET["size"] : false;

if(!$thumb && $Lando->config["host"] == "dropbox" && strpos(strtolower($Lando->config["host_root"]), "/public") === 0) {

	$account = $Lando->get_host_info();

	if(isset($account["uid"])) {	
		$full_path = $account["uid"]."/Lando/$site_title$path";
		$url = "http://dl.dropbox.com/u/".str_replace("%2F", "/", rawurlencode($full_path));
		
		header("Location: $url");
		exit();
	}
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