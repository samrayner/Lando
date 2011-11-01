<?php

class Page extends Publishable {
	public $subpages = array();
	
	//get functions
	public function subpages() {
		return $this->subpages;
	}
}