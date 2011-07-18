<?php

class Text extends Content {
	public $content;
	public $extension;
	public $author = "";
	
	public function __toString() {
		return $this->swap_includes($this->content);
	}
	
	private function swap_includes($content) {
		$regex = '\{\{\s*(\w+)(\s+(\w+:)?("[^"]*"|\w+|\d+|true|false))+\s*}}';
		
		//if has been converted to html (may have wrapped include in unwanted P tags so remove them)
		if(!in_array($this->extension, array("html", "htm")))
			$regex = "(?:<p>)?$regex(?:</p>)?";
	
		$content = preg_replace("~$regex~ie",
														'$this->process_include("\0", "\1")',
														$content);

		return $content;
	}
	
	private function process_include($str, $func) {
		$args = str_replace($func, "", $str);
		$allowed_funcs = array("snippet", "gallery", "slideshow", "collection");
		
		if(!in_array($func, $allowed_funcs) || !function_exists($func))
			return $str;
		
		preg_match_all('~\s+(?:(\w+):)?("[^"]*"|\w+|\d+|true|false)~', $args, $matches, PREG_SET_ORDER);
		
		$args = array(
			"title" => "",
			"size" => 0,
			"limit" => 0,
			"link_images" => null
		);
		
		foreach($matches as $match) {
			//if key undefined, default to title (allows simpler {{foo "Title"}} includes)
			if(!$match[1])
				$match[1] = "title";
			
			$args[$match[1]] = preg_replace('~^"|"$~', "", $match[2]);;
		}
	
		switch($func) {
			case "snippet": 
			case "collection": 
				$include = $func($args["title"]);
				break;
			case "gallery": 
			case "slideshow": 
				$include = $func($args["title"], $args["size"], $args["limit"], $args["link_images"]);
				break;
			default: 
				$include = false;
		}
		
		if(!$include)
			return $str;
			
		return $include;
	}
}