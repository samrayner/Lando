<?php

function admin_cookie() {
	global $Lando;

	if(!isset($_COOKIE['lando_password']))
		return false;

	return $_COOKIE['lando_password'] == $Lando->config['admin_password'];
}

function http_auth() {
	global $Lando;

	if(!isset($_SERVER['PHP_AUTH_PW']))
		return false;

	return $_SERVER['PHP_AUTH_PW'] == $Lando->config['admin_password'];
}