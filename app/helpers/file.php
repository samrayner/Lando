<?php

function rrmdir($dir) { 
	if(is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach($objects as $object) { 
			if($object != "." && $object != "..") { 
				if(filetype($dir."/".$object) == "dir")
					rrmdir($dir."/".$object); 
				else
					@unlink($dir."/".$object); 
			} 
		} 
		reset($objects); 
		@rmdir($dir);
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

function dir_size($dir) { 
	$size = 0; 

	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file)
		$size += $file->getSize(); 

	return $size; 
} 

function dir_file_count($dir) {
	$file_count = 0;
	
	$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);
	foreach($files as $name => $_) {
		if(substr(basename($name), 0, 1) != ".")
			$file_count++;
	}

	return $file_count;
}

function format_bytes($bytes, $unit="b") {
	$unit = strtolower($unit);
	$units = array("b","kb","mb","gb","tb");

	if(!in_array($unit, $units) || $unit == "b")
		return $bytes;
	
	$power = array_search($unit, $units)*10;
	
	return round($bytes/pow(2, $power), 2);
}

function inheritedTemplate($path_segs, $themeBase) {
	for($i = sizeof($path_segs)-1; $i > 0; $i--) {
		if(include_exists($themeBase.$path_segs[$i]."+.php"))
			return $path_segs[$i]."+";
	}
	
	return "page";
}