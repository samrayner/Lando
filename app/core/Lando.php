<?php

Class Lando {
	public $config;
	
	private $host;
	
 /**
	* Constructor
	*
	* @access public
	*
	* @param Array $config Configuration variables set in admin/on installation
	*/	
	function __construct($config) {
		//make config available to methods and helper functions
		$this->config = $config;
		
		$host_class = ucfirst($config["host"])."_Host";
		$this->host = new $host_class();
	}
}