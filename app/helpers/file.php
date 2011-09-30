<?php

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