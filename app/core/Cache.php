<?php

class Cache {
	public $collections = array();
	public $pages = array();
	public $posts = array();
	public $drafts = array();
	public $snippets = array();
	public $thumbs = array();
	public $account = array();

	public function __construct($type=null) {
		if($type)
			$this->load_single($type);
		else
			$this->load_all();
	}

	public function update($type, $content) {
		$this->$type = $content;
		return $this->save($type);
	}
	
	public function add($type, $content) {
		array_push($this->$type, $content);
	}
	
	private function load_all() {
		$types = get_object_vars($this);
		
		foreach($types as $type => $_)
			$this->load_single($type);
	}
	
	private function load_single($type) {
		$path = "app/cache/".$type.".php";
		
		if(file_exists($path))
			include_once $path;
		
		if(isset($cache))
			$this->$type = unserialize(htmlspecialchars_decode($cache, ENT_QUOTES));
	}
	
	public function save($type)	{
		if(!file_exists("app/cache"))
			mkdir("app/cache");
			
		return @file_put_contents("app/cache/$type.php", '<?php $cache = \''.htmlspecialchars(serialize($this->$type), ENT_QUOTES)."';");
	}
	
	public function age($type) {
		//if file doesn't exist return 0 for modified time
		return time()-(int)@filemtime("app/cache/$type.php");
	}
}
