<?php

class Controller {
	public $config;
	private $Model;
	
	public function __construct() {
		//make config available to methods and helper functions
		global $config;
		$this->config = $config;
		
		$this->Model = new Model();
	}
	
	public function get_host_account_info() {
		return $this->Model->get_host_account_info();
	}
}