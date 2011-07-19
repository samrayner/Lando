<?php 

abstract class Cloud_Host {
	protected $content_root;
	protected $parsable_exts;
	
	public function __construct() {
		global $config;
		$this->config = $config;
		$this->content_root = $config["host_root"]."/".$config["site_title"];
	}

	protected function filename_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_FILENAME);
	}

	protected function ext_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_EXTENSION);
	}
	
	protected function get_file_url($path) {
		return $this->config["site_root"]."/file.php/".trim_slashes($path);
	}
	
	protected function resolve_media_srcs($html, $dir) {
		if(preg_match_all('/<(?:img|audio|video|source)[^>]+src="([^"]*)"[^>]*>/i', $html, $tags)) {
			foreach($tags[1] as $src) {
				//if relative url
				if(strpos($src, ":") === false && strpos($src, "/get_file.php") === false) {
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

					$html = str_replace('"'.$src.'"', '"'.$resolved.'"', $html);
				}
			}
		}
		
		return $html;
  }
  
 	protected function extract_dimensions(&$title) {
 		$dims = array();
 	
		if(preg_match('~[^0-9a-z](?<w>[1-9]\d{0,4})x(?<h>[1-9]\d{0,4})(?:\W|$)~i', $title, $matches)) {
			$dims["width"] = $matches["w"];
			$dims["height"] = $matches["h"];
			$title = trim(str_replace($matches[0], " ", $title));
		}
	
		return $dims;
	}
	
	protected function extract_order(&$title) {
		$order = null;
	
		if(preg_match('~^(?<num>\d+)+\.\s*(?<title>.+)$~', $title, $matches)) {
			$order = $matches["num"];
			$title = $matches["title"];
		}
		
		return $order;
	}
	
	protected function manual_meta($content) {
		//reset when re-reading manual metadata
		$meta["tags"] = array();
		$meta["author"] = "";
	
		if(!$content)
			return $meta;
	
		$lines = preg_split('~\r?\n~', $content);
		$i = 0;
		
		//read line by line, if empty or metadata store and move to next line
		while(isset($lines[$i]) && (trim($lines[$i]) === "" || preg_match('~^\s*(?<key>\w+)\s*:\s*(?<val>.*)$~', $lines[$i], $prop))) {
			if(isset($prop)) {
			  $key = strtolower($prop["key"]);
    		$val = trim($prop["val"]);
    	
    		switch($key) {
    			case "slug":
    				$val = str_to_slug($val);
    				break;
    				
    			case "modified":
    			case "published":
    				$val = strtotime($val);
    				break;
    				
    			case "tags":
    				$tags = explode(",", $val);
    				$val = array();
    				
    				foreach($tags as $tag) {
    					$tag = trim($tag);
    					if($tag !== "")
    						$val[] = $tag;
    				}
    				break;
    		}
    		
    		if($val)
    			$meta[$key] = $val;
    	}
    	
    	unset($prop);
    	$i++;
		}
		
		$lines = array_slice($lines, $i);
		
		$meta["raw_content"] = implode("\n", $lines);
		
		return $meta;
	}
}