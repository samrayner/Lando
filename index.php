<?php
include "app/core/loader.php";

$themeBase = trim_slashes($theme_dir)."/";
$template = "404";
$url = current_path();
$current = new Page();

if($url == "/") {
	$current = $Lando->get_content();
	$template = $current ? "home" : "404";
}

if(preg_match('~^/([\w-]+)$~', $url, $matches)) {
	switch($matches[1]) {
		case "posts": 
			$template = "posts_all";
			break;
		case "drafts":
			$template = "drafts_all";
			break;
		case "rss":
			$template = "rss";
			break;
		default: 
			$current = $Lando->get_content();
		
			if(!$current)
				$template = "404";
			elseif(include_exists($themeBase.$matches[1].".php"))
				$template = $matches[1];
			else
				$template = "page";
	}
}

if(preg_match('~^/posts/from/(\d{4})(?:/(\d{2}))?(?:/(\d{2}))?$~', $url))
	$template = "posts_by_date";

elseif(preg_match('~^/posts/tagged/([\w\s\+-,]+)$~', $url))
	$template = "posts_by_tag";

elseif(preg_match('~^/([\w-]+)(?:/([\w-]+))+$~', $url, $matches)) {
	$current = $Lando->get_content();

	if(!$current)
		$template = "404";
	else {
		switch($matches[1]) {
			case "posts": 
				$template = "post";
				break;
			case "drafts":
				$template = "draft";
				break;
			default: 
				if(include_exists($themeBase.$matches[2].".php"))
					$template = $matches[2];
				else
					$template = "page";
		}
	}
}
	
//kick out to login if trying to view drafts
if(in_array($template, array("draft", "drafts_all")) && !admin_cookie())
	header("Location: $site_root/admin/login.php?redirect=drafts");

$helper_file = $themeBase."theme_functions.php";
if(include_exists($helper_file))
	include $helper_file;

if(!include_exists($themeBase.$template.".php")) {
	if(include_exists("app/templates/$template.php"))
		$themeBase = "app/templates/"; //fallback for missing optional custom templates
	else
		system_error("Missing Theme/Template", "The template file <em>$template.php</em> could not be found in <em>$theme_dir</em>.");
}

include_once $themeBase.$template.".php";