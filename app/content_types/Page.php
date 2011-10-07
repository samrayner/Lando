<?php

class Page extends Publishable {
	public $subpages = array(); //of type Page
	
	//get functions
	
	public function subpages() {
		return $this->subpages;
	}
}