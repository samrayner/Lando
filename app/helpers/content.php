<?php

function pages($limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("pages"), $limit, $offset, $filters, $year, $month, $day);
}

function posts($limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("posts"), $limit, $offset, $filters, $year, $month, $day);
}

function drafts($limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("drafts"), $limit, $offset, $filters, $year, $month, $day);
}

function collection($title, $limit=0, $offset=0, $filters=array()) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	global $Lando;
	return $Lando->filter_collection($Lando->get_content("collections", $title), $limit, $offset, $filters);
}

function gallery($title, $size=0, $limit=0, $offset=0, $filters=array(), $link_images=true) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	$collection = collection($title, $limit, $offset, $filters);
	
	if(!$collection)
		return false;
	
	return $collection->image_list_html("gallery", $size, $link_images);
}

function slideshow($title, $size=0, $limit=0, $offset=0, $filters=array(), $link_images=false) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	$collection = collection($title, $limit, $offset, $filters);
	
	if(!$collection)
		return false;
	
	return $collection->image_list_html("slideshow", $size, $link_images);
}

function snippet($title) {
	global $Lando;
	return $Lando->get_content("snippets", $title);
}

function page_nav($pages=null, $path=array()) {
	global $Lando;

	if(!$pages) { //first run-through
		$page_order = $Lando->config["page_order"];
	
		$html = '<nav class="page-nav">'."\n";
		$html .= page_nav($page_order);
		$html .= '</nav>';
		return $html;
	}
	
	$current_class = "current";
	$url = current_url();
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = "$tabs<ul>\n";

	foreach($pages as $page => $subpages) {
		if(isset($subpages["_hidden"]))
			continue;
	
		$path[] = $page;
		$path_str = "/".implode("/", $path)."/";
		
		$page = $Lando->get_content("pages", trim_slashes($path_str));
		
		if($url == "/")
			$url = "/home/";
		$current = (strpos($url, $path_str) === 0);

		$html .= "$tabs\t<li";
		if($current) $html .= ' class="'.$current_class.'"';
		
		$html .= ">\n$tabs\t\t".'<a href="'.$page->permalink.'">'.$page->title."</a>\n";

		if(!empty($subpages))
			$html .= page_nav($subpages, $path);

		array_pop($path);

		$html .= "$tabs\t</li>\n";
	}

	$html .= "$tabs</ul>\n";
	
	return $html;
}