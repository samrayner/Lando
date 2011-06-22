<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="description" content="<?= $site_description ?>" />
	
	<? if(url_segment(1) == "drafts") echo '<meta name="robots" content="noindex, nofollow" />' ?>
	
	<title><?= $site_title ?></title>
	
	<!-- Default styles -->
	<link rel="stylesheet" href="<?= $theme_dir ?>/css/screen.css" media="screen" />
	
	<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>