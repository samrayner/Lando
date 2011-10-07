<?php

class Snippet extends Text {
	public $created;
	
	//get functions
	
	public function created($format="U") {
		return date($format, $this->created);
	}
}