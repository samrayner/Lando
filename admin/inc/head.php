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
	
	<meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	
	<title><?php echo $title ?></title>

	<link rel="icon" href="" />
	<link rel="apple-touch-icon" href="" />

	<link rel="stylesheet" href="<?php echo $rel_root ?>css/admin.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
	<script src="<?php echo $rel_root ?>js/min/admin-min.js"></script>
	
</head>
<body>
<div id="wrapper">