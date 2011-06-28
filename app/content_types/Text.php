<?php

class Text extends Content {
	public $raw_content;
	public $content;
	public $extension;
	
	public function __toString() {
		return $this->swap_includes($this->content);
	}
	
	private function swap_includes($content) {
		$content = preg_replace('~(?:<p>)?\{\{\s*(\w+)(\s+(\w+:)?("[^"]*"|\w+|\d+|true|false))+\s*}}(?:</p>)?~ie',
														'$this->process_include("\0", "\1", "\2")',
														$content);

		return $content;
	}
	
	private function process_include($str, $func, $args) {
		$allowed_funcs = array("snippet", "gallery", "slideshow", "collection");
		
		if(!in_array($func, $allowed_funcs) || !function_exists($func))
			return $str;
		
		preg_match_all('~\s+(?:(\w+):)?("[^"]*"|\w+|\d+|true|false)~', $args, $matches, PREG_SET_ORDER);
		
		$args = array(
			"title" => "",
			"limit" => 0,
			"offset" => 0,
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
				$include = $func($args["title"], $args["limit"], $args["offset"], $args["link_images"]);
				break;
			default: 
				$include = false;
		}
		
		if(!$include)
			return $str;
			
		return compress_html($include);
	}
}