<?php

function rrmdir($dir) { 
	if(is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach($objects as $object) { 
			if($object != "." && $object != "..") { 
				if(filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
			} 
		} 
		reset($objects); 
		rmdir($dir);
	} 
}

function include_exists($file) {
  $paths = explode(":", get_include_path());
  
	if(file_exists($file))
		return true;
  
  foreach($paths as $path) {
    if(file_exists($path.'/'.$file))
    	return true;
  }
  	
  return false;
}

function filename_from_path($path) {
	return pathinfo(trim_slashes($path), PATHINFO_FILENAME);
}

function ext_from_path($path) {
	return strtolower(pathinfo(trim_slashes($path), PATHINFO_EXTENSION));
}