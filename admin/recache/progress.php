<?php

$cache_dir = $_SERVER['DOCUMENT_ROOT']."/app/cache";
$cache_list = glob("$cache_dir/*.php");
$last = sizeof($cache_list)-1;

function modified_sort($a, $b) {
	return filemtime($a) - filemtime($b);
}

usort($cache_list, "modified_sort");

function basenames($path) {
	return str_replace(".php", "", basename($path));
}

$cache_list = array_map("basenames", $cache_list);

echo empty($cache_list) ? "pages" : $cache_list[$last];