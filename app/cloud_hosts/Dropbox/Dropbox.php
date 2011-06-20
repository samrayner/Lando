<?php

require_once "DropLib.php";

class Dropbox extends Cloud_Host {
	private $API;
	
	public function __construct() {
		parent::__construct();
				
		$consumer_key 		= "whoen0lsbfo9c6y";
		$consumer_secret 	= "jxto2adt35bmayy";
		
		$config_file = "app/config/dropbox.php";
		
		if(file_exists($config_file)) {
			//use saved tokens
			include_once $config_file;
			$token_key 				= $tokens["token_key"];
			$token_secret 		= $tokens["token_secret"];
		}
		else {
			throw new Exception("NEED TO AUTH WITH DROPBOX!");
		}
		
		$this->API = new DropLib($consumer_key, $consumer_secret, $token_key, $token_secret);
		$this->API->setNoSSLCheck(true); //while developing locally
	}
	
	private function encode_path($path) {
		return str_replace("%2F", "/", rawurlencode($path));
	}
	
	private function get_file_path($meta, $exts=null, $filename="") {
		if(!$exts)
			$exts = array_flatten($this->parsers);
		
		if(!$filename) {
			//look for '!' prefix
			$match = array_search_recursive('~/!.*$~', $meta, "path", true);
		}
		else {
			//sanitize title for use in regex
			$filename = preg_replace('/([{}\(\)\^$&.\*\?\/\+\|\[\\\\]|\]|\-)/', "\\1", $title);
		}
		
		//if not found, get first parsable file
		if(!$match)
			$match = array_search_recursive("~$filename\.(".implode("|", $exts).')$~i', $meta, "path", true);
		
		if(!$match)
			return false;
		
		return end($match);
	}
	
	public function dir_contents($path, $dirs_only=true) {
		$path = $this->encode_path($path);
		$meta = $this->API->metadata($path);
		
		$items = array();
		
		foreach($meta["contents"] as $item) {
			if($dirs == $item["is_dir"])
				$items[] = basename($item["path"]);
		}
		
		return $items;
	}
	
	public function get_single($path) {
		$type = array_shift(explode("/", trim_slashes($path)));
	
		$path = $this->encode_path($this->content_root."/$path");
		
		try {
			$meta = $this->API->metadata($path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$meta["published"] = $meta["created"] = $meta["modified"] = strtotime($meta["modified"]);
		$meta["revision"] = $meta["revision"];
		
		if($type != "collections") {
			$meta["slug"] = basename($meta["path"]);
			
			if($type == "pages") {
				if(preg_match('~^(?<num>\d+)+\.\s*(?<slug>.+)$~', $meta["slug"], $matches)) {
					$meta["order"] = $matches["num"];
					$meta["slug"] = $matches["slug"];
				}
			}
		
			$main_file = ($type == "snippet") ? $path : $this->get_file_path($meta);
			
			if($main_file) {
				$file_meta = ($type == "snippet") ? $meta : $this->API->metadata($this->encode_path($main_file));
	
				$meta["file_path"] = $main_file;
				$meta["title"] = $this->filename_from_path($main_file);
				$meta["extension"] = $this->ext_from_path($main_file);
				$meta["modified"] = strtotime($file_meta["modified"]);
				$meta["revision"] = $file_meta["revision"];
				
				$meta["raw_content"] = $this->API->download($this->encode_path($main_file));
				$format = parent_key($this->parsers, $meta["extension"]);
				
				if($meta["raw_content"] && $format) {
					$parser_class = $format."_Parser";
					$Parser = new $parser_class();
					$meta["content"] = $Parser->parse($meta["raw_content"]);
				}	
			}
		}
		else
			$meta["title"] = basename($meta["path"]); //collection
		
		$type_class = ucfirst(substr($type, 0, -1)); //from lowercase plural
		
		$item = new $type_class($meta);
		return $item;
	}
}