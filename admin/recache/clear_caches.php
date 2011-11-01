<?php

$doc_root = $_SERVER['DOCUMENT_ROOT'];
$cache_dir = "$doc_root/app/cache";
include_once("$doc_root/app/helpers/file.php");

if(file_exists($cache_dir))
	rrmdir($cache_dir);