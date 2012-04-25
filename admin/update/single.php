<?php

$doc_root = dirname(dirname(dirname(__FILE__)));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;

//load existing config
$config_file = "$doc_root/app/config/config.php";
if(include_exists($config_file))
	include_once $config_file;

$site_root = $config["site_root"];
if(!$config["pretty_urls"])
	$site_root .= "/index.php";

include "$doc_root/admin/inc/auth.php";

include_once "$doc_root/app/core/Cache.php";
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