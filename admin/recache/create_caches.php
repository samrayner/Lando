<?php

$doc_root = $_SERVER['DOCUMENT_ROOT'];
include "$doc_root/app/core/loader.php";
$cache_dir = "$doc_root/app/cache";

function rrmdir($dir) { 
 if (is_dir($dir)) { 
   $objects = scandir($dir); 
   foreach ($objects as $object) { 
     if ($object != "." && $object != "..") { 
       if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
     } 
   } 
   reset($objects); 
   rmdir($dir); 
 } 
}

if(file_exists($cache_dir))
	rrmdir($cache_dir);
	
$pages 				= $Lando->get_content("pages");
$posts 				= $Lando->get_content("posts");
$drafts 			= $Lando->get_content("drafts");
$collections 	= $Lando->get_content("collections");
$snippets 		= $Lando->get_content("snippets");

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	
	<title>Creating Caches...</title>
	
	<script>window.onload = parent.Recache.done();</script>
</head>
<body>	
</body>
</html>