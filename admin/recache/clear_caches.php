<?php

$doc_root = $_SERVER['DOCUMENT_ROOT'];
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