<?php

//stop page load timing out on big recaches
set_time_limit(0);

$doc_root = dirname(dirname(dirname(__FILE__)));

include "$doc_root/app/core/loader.php";
include "$doc_root/admin/inc/auth.php";

//always refresh pages cache
$Lando->get_all_fresh("pages");

$Cache = new Cache();

$path = isset($_GET["path"]) ? trim_slashes($_GET["path"]) : false ;

if($path === false)
	exit("Must supply a path in GET.");

$cache_path = $path;

if($cache_path === "")
	$cache_path = "home";

//if deleting page cache
if(!preg_match('~^(pages|posts|drafts)/~', $cache_path))
	$cache_path = "pages/$cache_path/page";

$Cache->delete($cache_path);

$files_path = "files/".preg_replace('~/page$~', "", $cache_path);
$Cache->delete($files_path);

header("Location: $site_root/$path");