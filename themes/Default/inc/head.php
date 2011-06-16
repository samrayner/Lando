<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="author" content="<?= $dp->getDBName() ?>" />
	<meta name="description" content="<?= $dp->getSiteDescription() ?>" />
	
	<? if(strpos($dp->getUrl(), "/drafts") === 0) echo '<meta name="robots" content="noindex, nofollow" />' ?>
	
	<title><? if($dp->getTitle()) echo $dp->getTitle()." - " ?><?= $dp->getSiteTitle() ?></title>
	
	<!-- Default styles -->
	<link rel="stylesheet" href="<?= $dp->getThemeRoot() ?>/css/screen.css" media="screen" />
	
	<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>