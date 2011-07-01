<?php

class Content {
	public $title = "Untitled";
	public $modified;
	public $path;
	public $raw_content;
	
	public function __construct($data=null) {
		if($data)
			$this->import($data);
	}
	
	public function __toString() {
		return var_export($this->export(), true);
	}
	
	protected function import($data) {
		if(!is_array($data))
			return false;
		
		$vars = get_object_vars($this);
		
		foreach($data as $var => $value) {
			if(array_key_exists($var, $vars))
				$this->$var = $value;
		}
	}
	
	public function export() {
		return get_object_vars($this);
	}
}