<?php

function bootstrap_page_navbar($blog_text="Blog", $pages=null, $path=array()) {
	global $Lando;
	$page_order = $Lando->config["page_order"];

	if(!$pages) { //first run-through
		$html = '
		<div class="navbar navbar-fixed-top">
	    <div class="navbar-inner">
	      <div class="container">
	        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	          <span class="icon-bar"></span>
	        </a>
	        <a class="brand" href="'.$Lando->config["site_root"].'">'.$Lando->config["site_title"].'</a>
	        <div class="nav-collapse">';

		$pages = pages();

		$pages[] = new Page(array(
			"slug" => "posts",
			"title" => $blog_text,
			"permalink" => "/posts/"
		));

		$html .= bootstrap_page_navbar($blog_text, $pages);

		$html .= '
			    </div><!-- /.nav-collapse -->
	      </div>
	    </div><!-- /navbar-inner -->
	  </div><!-- /navbar -->';

		return $html;
	}
	
	$current_class = "active";
	$parent_class = "dropdown";
	$under_depth_limit = count($path) < 1;
	
	$url = current_path();
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = $tabs.'<ul class="';

	$html .= empty($path) ? "nav" : "dropdown-menu";

	$html .= '">'."\n";

	foreach($pages as $page) {
		$path[] = $page->slug();
		
		$current = $page_order;
		foreach($path as $next_key) {
			if(isset($current[$next_key]))
				$current = $current[$next_key];
		}
		
		if(!isset($current["_hidden"]) || $current["_hidden"] == false) {
			$path_str = "/".implode("/", $path)."/";
			
			if($url == "/")
				$url = "/home/";
			
			$current = (strpos($url, rtrim($path_str, "/")) === 0);
			$subpages = $page->subpages();
	
			$html .= "$tabs\t<li";
			
			$li_classes = array();
			
			if($current)
				$li_classes[] = $current_class;
				
			if(!empty($subpages) && $under_depth_limit)
				$li_classes[] = $parent_class;
			
			if(!empty($li_classes)) 
				$html .= ' class="'.implode(" ", $li_classes).'"';
			
			$html .= ">\n$tabs\t\t".'<a href="'.$page->permalink().'"';
			
			if(!empty($subpages) && $under_depth_limit) 
				$html .= ' class="dropdown-toggle" data-toggle="dropdown"';

			$html .= '>'.$page->title();

			if(!empty($subpages) && $under_depth_limit)
				$html .= ' <b class="caret"></b>';

			$html .= "</a>\n";
	
			if(!empty($subpages) && $under_depth_limit)
				$html .= bootstrap_page_navbar($blog_text, $subpages, $path);
	
			$html .= "$tabs\t</li>\n";
		}
		
		array_pop($path);
	}

	$html .= "$tabs</ul>\n";
	
	return $html;
}