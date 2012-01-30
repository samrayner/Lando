<?php

function admin_cookie() {
	global $Lando;

	if(!isset($_COOKIE['lando_password']))
		return false;

	return $_COOKIE['lando_password'] == $Lando->config['admin_password'];
}