<?php 

class Model {
	private $host;
	private $cache;

	function __construct() {
		global $config;
		$host_class = ucfirst($config["host"]);
		
		$this->host = new $host_class();
	}
	
	public function get_host_account_info() {
		return $this->host->account;
	}
}