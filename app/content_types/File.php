<?php

class File extends Content {
	public $extension;
	public $order;
	public $bytes;
	public $size;
	public $url;
	public $dynamic_url;
	
	public function __toString() {
		return $this->html();
	}
	
	public function html() {
		return '<a href="'.$this->url.'">'.$this->title.'</a>';
	}
}