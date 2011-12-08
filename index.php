<?php
include "app/core/loader.php";

/*
//if no config file exists, redirect to install
if(!file_exists("app/config/config.php"))
	//location: admin/install
*/

$themeBase = trim_slashes($theme_dir)."/";
$template = "404";
$url = current_path();
$current = new Page();

if($url == "/") {
	$current = $Lando->get_content();
	$template = "home";
}

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
	$template = "date-archive";

elseif(preg_match('~^/posts/tagged/([\w\s\+-,]+)$~', $url))
	$template = "tag-archive";

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
if(in_array($template, array("draft", "draft-list"))) {
	if(!isset($_COOKIE["lando_password"]) || $_COOKIE["lando_password"] != $Lando->config["admin_password"])
		header("Location: $site_root/admin/login.php?redirect=drafts");
}

if(!include_exists($themeBase.$template.".php")) {
	if(include_exists("app/templates/$template.php"))
		$themeBase = "app/templates/"; //fallback for missing optional custom templates
	else
		throw new Exception("Template file $template not found.");
}

include $themeBase.$template.".php";