<?php

//stop page load timing out on big moves
set_time_limit(0);

$doc_root = dirname(dirname(__FILE__));

include "$doc_root/app/core/loader.php";
include "$doc_root/admin/inc/auth.php";

if(!isset($_GET["slug"]))
	exit("Must supply a draft slug in GET.");

$slug = $Lando->publish_draft($_GET["slug"]);

if(!$slug)
	exit("Could not move folder from Drafts to Posts. Either it does not exist in Drafts or there were too many naming conflicts to resolve.");

header("Location: $site_root/posts/$slug");