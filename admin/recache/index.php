<?php

//stop page load timing out on big recaches
set_time_limit(0);

$doc_root = $_SERVER['DOCUMENT_ROOT'];
include "$doc_root/app/core/loader.php";

$valid_types = array("pages", "posts", "drafts", "collections", "snippets", "files");

//if no types set, recache all
$types = isset($_GET["type"]) ? explode(",", $_GET["type"]) : $valid_types;

$key = array_search("files", $types);

//delete files dir if requested
if($key !== false) {
	$cache_path = "$doc_root/app/cache/files";
	
	if(is_dir($cache_path))
		rrmdir($cache_path);
	
	//so as not to try and fetch files with get_content
	unset($types[$key]);
}

foreach($types as $type) {
	if(in_array($type, $valid_types)) {
		$cache_path = "$doc_root/app/cache/$type";
	
		if(is_dir($cache_path))
			rrmdir($cache_path);
		
		//create new ones
		$files = $Lando->get_content($type);

		//make sure content includes are cached (only parsed when called)
		foreach($files as $file) {
			if(method_exists($file, "content"))
				$file->content();
		}
	}
}

?>
Caches successfully refreshed.