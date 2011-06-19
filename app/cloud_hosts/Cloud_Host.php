<?php 

abstract class Cloud_Host {
	protected $content_root;
	protected $parsable_exts;
	
	public function __construct() {
		global $config;
		$this->content_root = $config["host_root"]."/".$config["site_title"];
		$this->parsable_exts = array_flatten($config["parsers"]);
	}

	protected function filename_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_FILENAME);
	}

	protected function ext_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_EXTENSION);
	}
}