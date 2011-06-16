<?php
include_once "classes/autoload.php";
$dp = new DropPub();

if(file_exists("functions.php"))
	include "functions.php";

//trim leading slash to make relative
$template = $dp->trimSlashes($dp->getThemeDir())."/404.php";

header("HTTP/1.1 404 Not Found");

if(file_exists($template)) {
	set_include_path(get_include_path().":".$_SERVER['DOCUMENT_ROOT'].$dp->getThemeDir());
	include $template;
	exit();
}
?>


<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	
	<title>Page Not Found - <?= $dp->getSiteTitle() ?></title>
</head>
<body>	

	<h1>Page Not Found</h1>
	<p>Sorry, we could not find the page you're looking for. Try going <a href="<?= $dp->getSiteRoot() ?>">home</a>.</p>

</body>
</html>