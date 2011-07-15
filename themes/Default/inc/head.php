<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="description" content="<?php echo $site_description ?>" />
	
	<? if(url_segment(1) == "drafts") echo '<meta name="robots" content="noindex, nofollow" />' ?>
	
	<title><?php echo $site_title ?></title>
	
	<!-- Default styles -->
	<link rel="stylesheet" href="<?php echo $theme_dir ?>/css/screen.css" media="screen" />
	
	<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>