<?php

class Controller {
	public $config;
	private $model;
	
 /**
	* Constructor
	*
	* @access public
	*
	* @param Array $config Configuration variables set in admin/on installation
	*/	
	function __construct() {
		//make config available to methods and helper functions
		global $config;
		$this->config = $config;
		
		$this->model = new Model();
	}
	
	public function get_host_account_info() {
		return $this->model->get_host_account_info();
	}
}