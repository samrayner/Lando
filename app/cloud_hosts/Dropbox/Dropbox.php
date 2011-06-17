<?php

require_once("DropLib.php");

class Dropbox extends Cloud_Host {
	private $api;
	
	function __construct() {
		global $config;
		
		$consumer_key 		= "whoen0lsbfo9c6y";
		$consumer_secret 	= "jxto2adt35bmayy";
		$token_key 				= $config["oauth_tokens"]["token_key"];
		$token_secret 		= $config["oauth_tokens"]["token_secret"];
		
		$this->api = new DropLib($consumer_key, $consumer_secret, $token_key, $token_secret);
		$this->api->setNoSSLCheck(true); //while developing locally
		
		$this->content_root = $config["host_root"]."/Lando/".$config["site_title"];
		$this->account = $this->api->accountInfo();
	}
}