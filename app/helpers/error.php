<?php

function system_error($title="Error", $message="An unknown error occured.") {
	$title = urlencode($title);
	$message = urlencode($message);

	global $Lando;
	$site_root = isset($Lando->config["site_root"]) ? $Lando->config["site_root"] : guess_site_root();
	
	header("Location: $site_root/app/core/error.php?title=$title&message=$message");
}