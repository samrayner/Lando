<?php

class Text extends Content {
	public $raw_content;
	public $content;
	public $extension;
	
	public function __toString() {
		if($this->content)
			return $this->content;
		
		return parent::__toString();
	}
}