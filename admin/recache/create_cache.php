<?php

$doc_root = $_SERVER['DOCUMENT_ROOT'];
include "$doc_root/app/core/loader.php";

$types = array("pages", "posts", "drafts", "collections", "snippets");

if(!isset($_GET["type"]) || !in_array($_GET["type"], $types))
	exit;
	
$files = $Lando->get_content($_GET["type"]);

//make sure content includes are cached (only parsed when called)
foreach($files as $file) {
	if(method_exists($file, "content"))
		$file->content();
}

