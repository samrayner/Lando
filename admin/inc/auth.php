<?php

if(!isset($Lando) && isset($config)) {
	$Lando = new stdClass();
	$Lando->config = $config;
}

if(!admin_cookie() && !http_auth()) {
	header('WWW-Authenticate: Basic realm="Log in to Lando"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'You are not authorized to view this page.';
	exit;
}