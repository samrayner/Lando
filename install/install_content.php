<?php

//stop page load timing out
set_time_limit(0);

$doc_root = dirname(dirname(__FILE__));
$content_root = "$doc_root/install/content";

include "$doc_root/app/core/loader.php";

if(is_dir($content_root))
	$Lando->install_content($content_root, $Lando->config["host_root"], "$doc_root/install/install_log.txt");