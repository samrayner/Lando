<?php

/*
//if no config file
if(!file_exists("app/config/config.php")) {
	//redirect to install
	return;
}
*/

include "app/core/loader.php";

$themeBase = trim_slashes($theme_dir)."/";
$template = "home";
$url = current_url();

if(preg_match('~^/([\w-]+)$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "date-archive";
			break;
		case "drafts":
			$template = "draft-list";
			break;
		case "rss":
			$template = "rss";
			break;
		default: 
			if(!$current)
				$template = "404";
			elseif(file_exists($themeBase.$matches[1].".php"))
				$template = $matches[1];
			else
				$template = "page";
	}
}

if(preg_match('~^/([\w-]+)(?:/([\w-]+))+$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "post";
			break;
		case "drafts":
			$template = "draft";
			break;
		default: 
			if(file_exists($themeBase.$matches[2].".php"))
				$template = $matches[2];
			else
				$template = "page";
	}
	
	if(!$current)
		$template = "404";
}

if(preg_match('~^/posts/from/(\d{4})(?:/(\d{2}))?(?:/(\d{2}))?$~', $url))
	$template = "date-archive";

if(preg_match('~^/posts/tagged/([\w\s\+-,]+)$~', $url))
	$template = "tag-archive";

if(!file_exists($themeBase.$template.".php")) {
	if(file_exists("app/templates/$template.php"))
		$themeBase = "app/templates/"; //fallback for missing optional custom templates
	else
		throw new Exception("Template file $template not found.");
}

include $themeBase.$template.".php";