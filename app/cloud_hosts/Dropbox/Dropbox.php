<?php

require_once "DropLib.php";

class Dropbox extends Cloud_Host {
	private $API;
	public $account = array();
	
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
		
		$this->account = $this->API->accountInfo();
		
		echo "\n\n<pre>\n"; print_r($this->get_post("new-post-title")); echo "\n</pre>\n\n";
	}
	
	private function encode_path($path) {
		return str_replace("%2F", "/", rawurlencode($path));
	}
	
	private function get_file_path($meta, $exts=null, $filename="") {
		if(!$exts)
			$exts = $this->parsable_exts;
		
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
	
	public function get_subdirs($path) {
		$path = $this->encode_path($path);
		$meta = $this->API->metadata($path);
		
		$dirs = array();
		
		foreach($meta["contents"] as $file) {
			if($file["is_dir"])
				$dirs[] = basename($file["path"]);
		}
		
		return $dirs;
	}
	
	public function get_post($slug) {
		$path = $this->encode_path($this->content_root."/posts/$slug");
		
		try {
			$meta = $this->API->metadata($path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$meta["slug"] = $slug;
		$meta["published"] = $meta["modified"] = strtotime($meta["modified"]);
		
		$main_file = $this->get_file_path($meta);
			
		if($main_file) {
			$file_meta = $this->API->metadata($this->encode_path($main_file));
			$meta["file_path"] = $main_file;
			$meta["title"] = $this->filename_from_path($file_meta["path"]);
			$meta["extension"] = $this->ext_from_path($file_meta["path"]);
			$meta["modified"] = strtotime($file_meta["modified"]);
			$meta["revision"] = $file_meta["revision"];
			
			$meta["raw_content"] = $this->API->download($this->encode_path($main_file));
			//$meta["content"] = parse($meta["raw_content"], $meta["extension"]);
		}
		
		$post = new Post($meta);
		return $post;
	}
}