<?php

class Text extends Content {
	public $raw_content;
	public $content;
	public $extension;
	
	public function __toString() {
		return $this->content;
	}
}