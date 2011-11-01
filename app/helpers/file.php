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