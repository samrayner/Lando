<?php

//stop page load timing out on big recaches
set_time_limit(0);

$doc_root = dirname(dirname(dirname(__FILE__)));
$cache_root = "$doc_root/app/cache";

include "$doc_root/app/core/loader.php";
include "$doc_root/admin/inc/auth.php";

$valid_types = array("pages", "posts", "drafts", "collections", "snippets");

//if no types set, recache all
$types = isset($_GET["type"]) ? array_map("trim", explode(",", $_GET["type"])) : $valid_types;

foreach($types as $type) {
	if(in_array($type, $valid_types)) {
		//remove existing caches
		if(is_dir("$cache_root/$type"))
			rrmdir("$cache_root/$type");
		
		if(is_dir("$cache_root/files/$type"))
			rrmdir("$cache_root/files/$type");
	
		//create new ones
		$files = $Lando->get_content($type);

		//make sure dynamic content is cached (only parsed when called)
		foreach($files as $File) {
			if(method_exists($File, "content"))
				$File->content();
		}
	}
}

$redirect = isset($_GET["redirect"]) ? trim_slashes($_GET["redirect"]) : "";

if($redirect)
	header("Location: $site_root/$redirect/");

echo 'Latest content fetched for '.implode(", ", $types).".";