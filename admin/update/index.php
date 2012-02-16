<?php

//stop page load timing out on big recaches
set_time_limit(0);

$doc_root = dirname(dirname(dirname(__FILE__)));
$cache_root = "$doc_root/app/cache";

include "$doc_root/app/core/loader.php";
include "$doc_root/admin/inc/auth.php";

$valid_types = array("collections", "snippets", "pages", "posts", "drafts");

//if no types set, recache all
$types = isset($_GET["type"]) ? array_map("trim", explode(",", $_GET["type"])) : $valid_types;

$delete_first = isset($_GET["delete"]) ? $_GET["delete"] : true;

foreach($types as $type) {
	if(in_array($type, $valid_types)) {
		if($delete_first) {
			//remove existing caches
			if(is_dir("$cache_root/$type"))
				rrmdir("$cache_root/$type");
			
			if(is_dir("$cache_root/files/$type"))
				rrmdir("$cache_root/files/$type");
		}
	
		//create new ones
		$items = $Lando->get_all_fresh($type);

		//make sure dynamic content is cached (only parsed when called)
		foreach($items as $Item) {
			if(method_exists($Item, "content"))
				$Item->content();
		}
	}
}

$redirect = isset($_GET["redirect"]) ? trim_slashes($_GET["redirect"]) : "";

if($redirect)
	header("Location: $site_root/$redirect/");

echo 'Latest content fetched for '.implode(", ", $types).".";