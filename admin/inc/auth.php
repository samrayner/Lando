<?php

if(!admin_cookie())
	header("Location: ".$config["site_root"]."/admin/login.php?redirect=admin");