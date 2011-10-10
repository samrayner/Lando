<?php

$cache_dir = $_SERVER['DOCUMENT_ROOT']."/app/cache";
$caches = array("pages","posts","drafts","collections","snippets");

$current = $caches[0];

//if cache folder is older than 5 seconds, don't output anything
//prevents saying all are complete before directory is deleted
if(file_exists($cache_dir) && (time() - filemtime($cache_dir) > 5)) {
	echo $current;
	exit;
}

for($i=1; $i < sizeof($caches); $i++) {
	if(file_exists("$cache_dir/".$caches[$i-1].".php"))
		$current = $caches[$i];
}

echo $current;

