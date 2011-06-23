<?php header("HTTP/1.1 404 Not Found"); ?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	
	<title>Page Not Found - <?= $site_title ?></title>
</head>
<body id="404">	

	<h1>Page Not Found</h1>
	<p>Sorry, we could not find the page you're looking for. Try going <a href="<?= $site_root ?>">home</a>.</p>

</body>
</html>