<?php

class Cache {
	public $pages = array();
	public $posts = array();
	public $drafts = array();
	public $snippets = array();
	public $collections = array();
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
		$path = $_SERVER['DOCUMENT_ROOT']."/app/cache/".$type.".php";
		
		if(include_exists($path))
			include_once $path;
		
		if(isset($cache))
			$this->$type = unserialize(htmlspecialchars_decode($cache, ENT_QUOTES));
	}
	
	public function save($type)	{
		$dir = $_SERVER["DOCUMENT_ROOT"]."/app/cache";
	
		if(!file_exists($dir))
			mkdir($dir);
			
		return @file_put_contents("$dir/$type.php", '<?php $cache = \''.htmlspecialchars(serialize($this->$type), ENT_QUOTES)."';");
	}
	
	public function age($type) {
		//if file doesn't exist return 0 for modified time
		return time()-(int)@filemtime($_SERVER['DOCUMENT_ROOT']."app/cache/$type.php");
	}
	
	public function top_level($type) {
		$names = array();
		
		switch($type) {
			case "pages":
			case "posts":
			case "drafts":
				$key = "slug";
				break;
			case "collections":
			case "snippets":
			case "thumbs":
				$key = "title";
				break;
			default:
				return array();
		}
		
		foreach($this->$type as $item) {
			if(isset($item->$key))
				$names[] = $item->$key;
		}
		
		return $names;
	}
}
