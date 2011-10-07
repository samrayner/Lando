<?php

class File extends Content {
	public $extension;
	public $order;
	public $bytes;
	public $url;
	public $mime_type;
	
	public function __toString() {
		return $this->html();
	}
	
	public function html() {
		return '<a href="'.$this->url.'">'.$this->title.'</a>';
	}
	
	//get functions
	
	public function extension() {
		return $this->extension;
	}

	public function order() {
		return $this->order;
	}

	public function bytes() {
		return $this->bytes;
	}

	public function size($unit="kb") {
		$unit = strtolower($unit);
		$units = array("b","kb","mb","gb","tb");
	
		if(!in_array($unit, $units) || $unit == "b")
			return $this->bytes;
		
		$power = array_search($unit, array_keys($a))*10;
		
		return round($this->bytes/pow(2, $power), 2);
	}

	public function url() {
		return $this->url;
	}

	public function mime_type() {
		return $this->mime_type;
	}
}