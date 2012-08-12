<?php

function pages($limit=0, $offset=0, $filter=array(), $year=0, $month=0, $day=0) {
		//extract if array of arguments passed
		if(func_num_args() == 1) {
			$arg1 = func_get_arg(0);
			if(is_array($arg1))
				extract($arg1);
		}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("pages"), $limit, $offset, $filter, $year, $month, $day);
}

function page($path) {
	global $Lando;
	return $Lando->get_content("pages", $path);
}

function posts($limit=0, $offset=0, $filter=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("posts"), $limit, $offset, $filter, $year, $month, $day);
}

function post($slug) {
	global $Lando;
	return $Lando->get_content("posts", $slug);
}

function drafts($limit=0, $offset=0, $filter=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("drafts"), $limit, $offset, $filter, $year, $month, $day);
}

function draft($slug) {
	global $Lando;
	return $Lando->get_content("drafts", $slug);
}

function snippets($limit=0, $offset=0, $filter=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("snippets"), $limit, $offset, $filter, $year, $month, $day);
}

function snippet($filename) {
	global $Lando;
	return $Lando->get_content("snippets", $filename);
}

function collections($limit=0, $offset=0, $filter=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("collections"), $limit, $offset, $filter, $year, $month, $day);
}

function collection($title, $limit=0, $offset=0, $filter=array()) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	//if args passed as array but no title
	if(is_array($title))
		return false;

	global $Lando;
	return $Lando->filter_collection($Lando->get_content("collections", $title), $limit, $offset, $filter);
}

function gallery($title, $size=0, $limit=0, $offset=0, $filter=array(), $link_images=true) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	//if args passed as array but no title
	if(is_array($title))
		return false;

	$Collection = collection($title, $limit, $offset, $filter);
	
	if(!$Collection)
		return false;
	
	return $Collection->image_list_html("gallery", $size, $link_images);
}

function slideshow($title, $size=0, $limit=0, $offset=0, $filter=array(), $link_images=false) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	//if args passed as array but no title
	if(is_array($title))
		return false;

	$Collection = collection($title, $limit, $offset, $filter);
	
	if(!$Collection)
		return false;
	
	return $Collection->image_list_html("slideshow", $size, $link_images);
}

function get_file($path, $thumb_size=false) {
	global $Lando;
	return $Lando->get_file($path, $thumb_size);
}

function tags($items = null) {
	if(!$items)
		$items = posts();

	$all_tags = array();
	
	foreach($items as $Item) {
		if($item_tags = $Item->metadata("tags"))
			$all_tags = array_merge($all_tags, $item_tags);
	}
	
	$tag_counts = array_count_values(array_map("strtolower", $all_tags));
	ksort($tag_counts);
	
	return $tag_counts;
}