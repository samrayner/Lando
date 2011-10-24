<?php

$doc_root = $_SERVER['DOCUMENT_ROOT'];
include "$doc_root/app/core/loader.php";

$types = array("pages", "posts", "drafts", "collections", "snippets");

if(!isset($_GET["type"]) || !in_array($_GET["type"], $types))
	exit;
	
$Lando->get_content($_GET["type"]);
?>
Cache refresh complete