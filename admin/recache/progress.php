<?php

$cache_dir = $_SERVER['DOCUMENT_ROOT']."/app/cache";
$caches = array("pages","posts","drafts","collections","snippets");

//if cache folder is older than 5 seconds, don't output anything
//prevents saying all are complete before directory is deleted
if(file_exists($cache_dir) && (time() - filemtime($cache_dir) > 5))
	return false;

echo "<ul>";
foreach($caches as $i => $type) {

	echo '<li';

	if(file_exists("$cache_dir/$type.php"))
		echo ' class="done">Cached '.$type;
	else {
		if(isset($caches[$i-1]) && file_exists("$cache_dir/".$caches[$i-1].".php"))
			echo ' class="current"';
		echo ">Caching $type&hellip;";
	}
	
	echo "</li>";
}
echo "</ul>";