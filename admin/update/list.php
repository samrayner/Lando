<?php

$doc_root = dirname(dirname(dirname(__FILE__)));

include "$doc_root/app/core/loader.php";
include "$doc_root/admin/inc/auth.php";

$valid_types = array("pages", "posts", "drafts", "collections", "snippets");

//if no types set, relist all
$types = isset($_GET["type"]) ? array_map("trim", explode(",", $_GET["type"])) : $valid_types;

foreach($types as $type) {
	if(in_array($type, $valid_types)) {
		//create new ones
		$items = $Lando->get_all_fresh($type);

		//make sure dynamic content is cached (only parsed when called)
		foreach($items as $Item) {
			if(method_exists($Item, "content"))
				$Item->content();
		}
	}
}

$redirect = isset($_GET["redirect"]) ? trim_slashes($_GET["redirect"]) : false;

if($redirect)
	header("Location: $site_root/$redirect/");

echo 'Latest content fetched for '.implode(", ", $types).".";