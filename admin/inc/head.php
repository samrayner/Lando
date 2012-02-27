<?php

$segs = explode("/", preg_replace('~/index.php$~', "", current_url()));
$title = "Lando";

switch(end($segs)) {
	case "admin":
		$title .= " Admin";
		break;
	case "login.php":
		$title .= " Login";
		break;
}
	
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<link rel="dns-prefetch" href="//ajax.googleapis.com">
	
	<meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	
	<title><?php echo $title ?></title>

	<link rel="stylesheet" href="css/admin.css" />

	<script>
		(function () {
			var filename;
			if (navigator.platform === 'iPad') {
				filename = window.orientation === 90 || window.orientation === -90 ? 'splash-1024x748.png' : 'splash-768x1004.png';
			} 
			else {
				filename = window.devicePixelRatio === 2 ? 'splash-640x920.png' : 'splash-320x460.png';
			}
			document.write('<link rel="apple-touch-startup-image" href="images/' + filename + '"/>' );
		})();
	</script>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<script src="js/min/admin-min.js"></script>
	
</head>
<body>
<div id="wrapper">