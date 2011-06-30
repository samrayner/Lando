<?php 

include "app/core/loader.php";

$path = urldecode($_GET["path"]);
$thumb = isset($_GET["size"]) ? $_GET["size"] : false;

$File = $Lando->get_file($path, $thumb);

if(!$File) {
	$custom_404 = trim_slashes($theme_dir)."/404.php";
	if(file_exists($custom_404))
		include $custom_404;
	else
		include "app/templates/404.php";
}

header('Content-Type: '.$File->mime_type);

$source = $File->raw_content;

if($thumb)
	$source = base64_decode($source);

echo $source;

?>