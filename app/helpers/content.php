<?php

function pages($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("pages"), $limit, $offset, $year, $month, $day);
}

function posts($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("posts"), $limit, $offset, $year, $month, $day);
}

function drafts($limit=0, $offset=0, $year=0, $month=0, $day=0) {
	global $Lando;
	return $Lando->filter_content($Lando->get_content("drafts"), $limit, $offset, $year, $month, $day);
}

function snippet($title) {
	global $Lando;
	return $Lando->get_content("snippets", $title);
}

function collection($title) {
	global $Lando;
	return $Lando->get_content("collections", $title);
}

function gallery($title, $size=0, $limit=0, $link_images=true) {
	$collection = collection($title);
	
	if(!$collection)
		return false;
	
	return $collection->image_list_html("gallery", $size, $limit, $link_images);
}

function slideshow($title, $size=0, $limit=0, $link_images=false) {
	$collection = collection($title);
	
	if(!$collection)
		return false;
	
	return $collection->image_list_html("slideshow", $size, $limit, $link_images);
}

function page_nav($pages=null, $path=array()) {
	if(!$pages) { //first run-through
		$html = '<nav class="page-nav">'."\n";
		$html .= page_nav(pages());
		$html .= '</nav>';
		return $html;
	}
	
	$current_class = "current";
	$url = current_url();
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = "$tabs<ul>\n";

	foreach($pages as $page) {
		$path[] = $page->slug;
		$path_str = "/".implode("/", $path)."/";
		
		if($url == "/")
			$url = "/home/";
		$current = (strpos($url, $path_str) === 0);

		$html .= "$tabs\t<li";
		if($current) $html .= ' class="'.$current_class.'"';
		
		$html .= ">\n$tabs\t\t".'<a href="'.$page->permalink.'">'.$page->title."</a>\n";

		if(!empty($page->subpages))
			$html .= page_nav($page->subpages, $path);

		array_pop($path);

		$html .= "$tabs\t</li>\n";
	}

	$html .= "$tabs</ul>\n";
	
	return $html;
}