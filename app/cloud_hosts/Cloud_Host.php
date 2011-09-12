<?php 

abstract class Cloud_Host {
	protected $content_root;
	
	public function __construct() {
		global $config;
		$this->config = $config;
	}

	protected function filename_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_FILENAME);
	}

	protected function ext_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_EXTENSION);
	}
	
	protected function sanitize_path($path) {
		$new_path = explode("/", $path);
		$new_path[sizeof($new_path)-1] = str_to_slug(end($new_path));
		return implode("/", $new_path);
	}
	
	protected function get_file_url($path) {
		return $this->config["site_root"]."/file.php/".trim_slashes($path);
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