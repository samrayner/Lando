<?php 

abstract class Cloud_Host {
	protected $config;

	public function __construct($config) {
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
	
	protected function manual_meta(&$raw_content) {
		$meta = array();
	
		if(!$raw_content)
			return $meta;
	
		$lines = preg_split('~\r?\n~', $raw_content);
		$i = 0;
		
		//read line by line. if empty or metadata, store and move to next line
		while(isset($lines[$i]) && (trim($lines[$i]) === "" || preg_match('~^\s*(?<key>\w+)\s*:\s*(?<val>.*)$~', $lines[$i], $prop))) {
			if(isset($prop)) {
			  $key = strtolower($prop["key"]);
    		$val = trim($prop["val"]);
    	
    		switch($key) {
    			case "slug":
    				$val = str_to_slug($val);
    				break;
    			
    			case "created":
    			case "modified":
    			case "published":
    				$val = strtotime($val);
    				break;
    				
    			default:
    				//if key is plural (e.g. tags, authors)
    				if(substr($key, -1) == "s") {
	    				$items = explode(",", $val);
	    				$val = array();
	    				
	    				foreach($items as $item) {
	    					$item = trim($item);
	    					if($item !== "")
	    						$val[] = $item;
	    				}
	    			}
    		}
    		
    		if($val)
    			$meta[$key] = $val;
    	}
    	
    	unset($prop);
    	$i++;
		}
		
		$lines = array_slice($lines, $i);
		
		$raw_content = implode("\n", $lines);
		
		return $meta;
	}
}