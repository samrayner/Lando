<?php

include "app/core/loader.php";

$themeBase = trim_slashes(theme_dir())."/";
$template = "home.php";

$url = trim_slashes(current_url());

if(preg_match('~^([a-z0-9-_]+)$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "post-archive.php";
			break;
		case "drafts":
			$template = "draft-list.php";
			break;
		default: 
			if(file_exists($themeBase.$matches[1].".php"))
				$template = $matches[1].".php";
			else
				$template = "page.php";
	}
}

if(preg_match('~^([a-z0-9-_]+)(?:/([a-z0-9-_]+))+$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "post.php";
			break;
		case "drafts":
			$template = "draft.php";
			break;
		default: 
			if(file_exists($themeBase.$matches[2].".php"))
				$template = $matches[2].".php";
			else
				$template = "page.php";
	}
}

if(preg_match('~^posts/from/(\d{4})(?:/(\d{2}))?(?:/(\d{2}))?$~', $url, $matches)) {
	$template = "post-archive.php";
	if(isset($matches[1]))
		$_GET["year"] = $matches[1];
	if(isset($matches[2]))
		$_GET["month"] = $matches[2];
	if(isset($matches[3]))
		$_GET["day"] = $matches[3];
}

if(!file_exists($themeBase.$template))
	throw new Exception("Template file $template not found.");

set_include_path(get_include_path().":".$_SERVER['DOCUMENT_ROOT'].theme_dir());

include $themeBase.$template;