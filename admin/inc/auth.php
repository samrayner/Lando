<?php

if(!isset($_COOKIE['lando_password']) || $_COOKIE['lando_password'] != $config['admin_password'])
	header("Location: ".$config["site_root"]."/admin/login.php?redirect=admin");