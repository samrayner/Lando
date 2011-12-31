<?php

class Publishable extends Text {
	public $slug;
	public $file_path;
	public $permalink;
	public $published;
	
	//get functions
	
	public function slug() {
		return $this->slug;
	}

	public function file_path() {
		return $this->file_path;
	}

	public function permalink() {
		global $Lando;
		
		$permalink = $Lando->config["site_root"];
		
		if(!$Lando->config["pretty_urls"])
			$permalink .= "/index.php";
		
		return $permalink.$this->permalink;
	}

	public function published($format="U") {
		return date($format, $this->published);
	}
}