<?php

include "app/core/Cache-new.php";

$Cache = new Cache();

$lol = array("lol");

echo "\n\n<pre>\n"; print_r($Cache->get_single("pages/posts/markdown-post", $lol)); echo "\n</pre>\n\n";


?>