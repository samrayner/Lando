<?php

class Text extends File {
	public $raw_content;
	public $manual_metadata = array();
	
	private function swap_includes($content) {
		$regex = '\{\{\s*(\w+)(\s+(\w+:)?("[^"]*"|\w+|\d+|true|false))+\s*}}';

		$parse 		= "(?<!\\\)$regex";
		$noparse 	= "\\\($regex)";
	
		$content = preg_replace("~$parse~ie",
														'$this->process_include("$0", "$1")',
														$content);
		
		$content = preg_replace("~$noparse~i", "$1", $content);

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
	
		$include = $func($args);
		
		if(!$include || (is_object($include) && !method_exists($include, "__toString")))
			return $str;
			
		return compress_html((string)$include);
	}

	private function get_file_url($path, $thumb=false) {
		global $Lando;
		$path = trim_slashes($path);
		$File = $Lando->get_file($path, $thumb);
		
		if(!$File)
			return false;
		
		return $File->url();
	}
	
	private function resolve_media_srcs($content, $dir) {
		if(preg_match_all('/<(?<tag>img|audio|video|source)[^>]+src="(?<src>[^"]*)"[^>]*>/i', $content, $elements)) {
			foreach($elements["src"] as $i => $src) {
				$is_img = ($elements["tag"][$i] == "img");

				//if relative url
				if(strpos($src, ":") === false) {
					if(strpos($src, "/") === 0) {
						$resolved = substr($src, 1); //resolve relative to site root
					}
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

						$resolved = $dir."/$resolved"; //resolve to current dir
					}

					$new_src = $this->get_file_url($resolved);
					
					if($new_src)
						$content = str_replace('"'.$src.'"', '"'.$new_src.'"', $content);
				}
			}
		}
		
		return $content;
  }
  
  //get functions
	public function metadata($key=null) {
		if(!$key)
			return $this->manual_metadata;

		$key = strtolower($key);
	
		if(!isset($this->manual_metadata[$key]))
			return false;
		
		return $this->manual_metadata[$key];
	}
  
  public function content() {
		global $Lando;
		
		//swap in include content
		$content = $this->swap_includes($this->raw_content);
		
		//parse to HTML using appropriate parser
		$content = $this->to_html($content);
		
		//make path relative to content root
		$rel_path = str_replace($Lando->config["host_root"], "", $this->path);
		
		$content = $this->resolve_media_srcs($content, $rel_path);
		
		if($Lando->config["smartypants"] && function_exists("SmartyPants"))
			$content = SmartyPants($content);
		
		return $content;
	}
	
	public function raw_content() {
		return $this->raw_content;
	}
}