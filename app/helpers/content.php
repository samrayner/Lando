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

function page($path) {
	global $Lando;
	return $Lando->get_content("pages", $path);
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

function post($slug) {
	global $Lando;
	return $Lando->get_content("posts", $slug);
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

function draft($slug) {
	global $Lando;
	return $Lando->get_content("drafts", $slug);
}

function snippets($limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("snippets"), $limit, $offset, $filters, $year, $month, $day);
}

function snippet($title) {
	global $Lando;
	return $Lando->get_content("snippets", $title);
}

function collections($limit=0, $offset=0, $filters=array(), $year=0, $month=0, $day=0) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}
	
	global $Lando;
	return $Lando->filter_content($Lando->get_content("collections"), $limit, $offset, $filters, $year, $month, $day);
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

	$Collection = collection($title, $limit, $offset, $filters);
	
	if(!$Collection)
		return false;
	
	return $Collection->image_list_html("gallery", $size, $link_images);
}

function slideshow($title, $size=0, $limit=0, $offset=0, $filters=array(), $link_images=false) {
	//extract if array of arguments passed
	if(func_num_args() == 1) {
		$arg1 = func_get_arg(0);
		if(is_array($arg1))
			extract($arg1);
	}

	$Collection = collection($title, $limit, $offset, $filters);
	
	if(!$Collection)
		return false;
	
	return $Collection->image_list_html("slideshow", $size, $link_images);
}

function page_nav($blog_text="Blog", $pages=null, $path=array()) {
	global $Lando;
	$page_order = $Lando->config["page_order"];

	if(!$pages) { //first run-through
		$html = '<nav class="page-nav">'."\n";

		$pages = pages();

		$pages[] = new Page(array(
			"slug" => "posts",
			"title" => $blog_text,
			"permalink" => "/posts/"
		));

		$html .= page_nav($blog_text, $pages);
		$html .= '</nav>';
		return $html;
	}
	
	$active_class = "current";
	$parent_class = "parent";
	
	$url = current_path();
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = "$tabs<ul>\n";

	foreach($pages as $Page) {
		$path[] = $Page->slug();
		
		$active = $page_order;
		foreach($path as $next_key) {
			if(isset($active[$next_key]))
				$active = $active[$next_key];
		}
		
		if(!isset($active["_hidden"]) || $active["_hidden"] == false) {
			$path_str = "/".implode("/", $path)."/";
			
			if($url == "/")
				$url = "/home/";
			
			$active = (strpos($url, rtrim($path_str, "/")) === 0);
			$subpages = $Page->subpages();
	
			$html .= "$tabs\t<li";
			
			$classes = array();
			
			if($active)
				$classes[] = $active_class;
				
			if(!empty($subpages))
				$classes[] = $parent_class;
			
			if(!empty($classes)) 
				$html .= ' class="'.implode(" ", $classes).'"';
			
			$html .= ">\n$tabs\t\t".'<a href="'.$Page->permalink().'">'.$Page->title()."</a>\n";
	
			if(!empty($subpages))
				$html .= page_nav($blog_text, $subpages, $path);
	
			$html .= "$tabs\t</li>\n";
		}
		
		array_pop($path);
	}

	$html .= "$tabs</ul>\n";
	
	return $html;
}