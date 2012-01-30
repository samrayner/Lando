<?php

//stop page load timing out on big recaches
set_time_limit(0);

$doc_root = dirname(dirname(dirname(__FILE__)));
$cache_root = "$doc_root/app/cache";

include "$doc_root/app/core/loader.php";

$valid_types = array("pages", "posts", "drafts", "collections", "snippets", "files");

//if no types set, recache all
$types = isset($_GET["type"]) ? array_map("trim", explode(",", $_GET["type"])) : $valid_types;

//remove "files" from list as can't cache in bulk
$key = array_search("files", $types);

if($key !== false) {
	if(is_dir("$cache_root/files"))
		rrmdir("$cache_root/files");
	
	unset($types[$key]);
}

foreach($types as $type) {
	if(in_array($type, $valid_types)) {
		//remove existing caches
		if(is_dir("$cache_root/$type"))
			rrmdir("$cache_root/$type");
	
		//create new ones
		$files = $Lando->get_content($type);

		//make sure content includes are cached (only parsed when called)
		foreach($files as $file) {
			if(method_exists($file, "content"))
				$file->content();
		}
	}
}

$redirect = isset($_GET["redirect"]) ? trim_slashes($_GET["redirect"]) : "";

if($redirect)
	header("Location: $site_root/$redirect/");

echo 'Latest content fetched for '.implode(", ", $types).".";