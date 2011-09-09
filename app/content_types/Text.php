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
		return $this->parse_content();
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
		
		//make path relative to content root
		global $Lando;
		$rel_path = str_replace($Lando->config["host_root"], "", $this->path);
		
		$content = $this->resolve_media_srcs($content, $rel_path);
			
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
			"offset" => 0,
			"filters" => array(),
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
				$include = $func($args)->list_html();
				break;
			case "gallery": 
			case "slideshow": 
				$include = $func($args);
				break;
			default: 
				$include = false;
		}
		
		if(!$include)
			return $str;
			
		return compress_html($include);
	}

	private function get_file_url($path) {
		global $Lando;
		return $Lando->config["site_root"]."/file.php/".trim_slashes($path);
	}
	
	private function resolve_media_srcs($content, $dir) {
		if(preg_match_all('/<(?:img|audio|video|source)[^>]+src="([^"]*)"[^>]*>/i', $content, $tags)) {
			foreach($tags[1] as $src) {
				//if relative url
				if(strpos($src, ":") === false && strpos($src, "/file.php") === false) {
					if(strpos($src, "/") === 0)
						$resolved = $this->get_file_url(substr($src, 1)); //resolve relative to site root
					else {
						$src_segs = explode("/", trim_slashes($src));
						$dir_segs = explode("/", trim_slashes($dir));
						
						while(isset($src_segs[0]) && $src_segs[0] == "..") {
							array_pop($dir_segs); //go up one dir
							array_shift($src_segs); //move on to next segment
						}
						
						$dir 			= implode("/", $dir_segs);
						$resolved = implode("/", $src_segs);
						
						$resolved = preg_replace('~^./~', '', $resolved);
						
						$resolved = $this->get_file_url($dir."/$resolved"); //resolve to current dir
					}

					$content = str_replace('"'.$src.'"', '"'.$resolved.'"', $content);
				}
			}
		}
		
		return $content;
  }
}