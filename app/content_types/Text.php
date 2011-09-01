<?php

class Text extends Content {
	private $content;
	
	public $format;
	public $extension;
	public $author = "";
	
	//parse calls to content
	public function __get($prop) {
	  if($prop == "content")
	  	return $this->parse_content();
	  
	  //otherwise give error for invald property
  	$trace = debug_backtrace();
    trigger_error(
        'Undefined property via __get(): ' . $prop .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
    return null;
	}
	
	public function __toString() {
		return $this->__get("content");
	}
	
	public function parse_content() {
		$content = $this->swap_includes($this->raw_content);
	  
		if($this->raw_content && $this->format) {
			$parser_class = $this->format."_Parser";
			if(class_exists($parser_class)) {
				$Parser = new $parser_class();
				$content = $Parser->parse($content);
			}
		}
			
		return $content;
	}
	
	private function swap_includes($content) {
		$regex = '\{\{\s*(\w+)(\s+(\w+:)?("[^"]*"|\w+|\d+|true|false))+\s*}}';
	
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
				$include = $func($args["title"])->parse_content();
				break;
			case "collection": 
				$include = $func($args["title"])->list_html();
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
			
		return compress_html($include);
	}
}