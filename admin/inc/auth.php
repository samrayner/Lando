<?php

if(!isset($_COOKIE['admin_password']) || $_COOKIE['admin_password'] != $config['admin_password'])
	header("Location: ".$config["site_root"]."/admin/login.php?redirect=admin");