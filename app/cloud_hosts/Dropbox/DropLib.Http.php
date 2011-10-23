<?php
/**
 * DropLib - DropBox API Class
 *
 * @package DropLib
 * @version 2.0.2
 * @copyright Copyright 2011 by Jonas Doebertin. All rights reserved.
 * @author Jonas Doebertin
 * @license Sold exclusively on CodeCanyon
 */

class DropLib_Http extends DropLib_Base{
	
	/**
	 * Perform SSL certificate validation
	 */
	protected $sslCheck;

	/**
	 * oAuthConsumer object
	 */
	protected $OAuthConsumer;
	
	/**
	 * OAuthToken object
	 */
	protected $OAuthToken = null;
	
	/**
	 * OAuthSignatureMethod object
	 */
	protected $OAuthSignatureMethod;
	
	/**
	 * Constructor
	 *
	 * Create oAuth consumer, token and signature method objects and set parameters
	 */
	public function __construct($params){
		
		$this->OAuthConsumer = new OAuthConsumer($params['consumerKey'], $params['consumerSecret']);
		if($this->strParamsSet($params['tokenKey'], $params['tokenSecret'])){
			$this->OAuthToken = new OAuthToken($params['tokenKey'], $params['tokenSecret']);
		}
		$this->OAuthSignatureMethod = new OAuthSignatureMethod_HMAC_SHA1;
		$this->sslCheck = $params['sslCheck'];
	}
	
	/**
	 * Set oAuth token
	 */
	public function setToken($key, $secret){
		
		if(!$this->strParamsSet($key, $secret)){
			throw new DropLibException_InvalidArgument;
		}
		
		if(is_null($this->OAuthToken)){	
			$this->OAuthToken = new OAuthToken($key, $secret);			
		} else{
			$this->OAuthToken->key = $key;
			$this->OAuthToken->secret = $secret;
		}
		
	}
	
	/**
	 * Returns the current oAuth token
	 */
	public function getToken(){
		
		return (is_null($this->OAuthToken)) ? null : array(
			'key' => $this->OAuthToken->key,
			'secret' => $this->OAuthToken->secret
		);
		
	}
	
	/**
	 * 
	 */
	public function fetch($url, $params = null, $useToken = true, $file = null){
		
		$params = (is_array($params)) ? $params : array();
		
		/**
		 * Check for token and sign request
		 */
		if($useToken and is_null($this->OAuthToken)){
			throw new DropLibException_OAuth('No oAuth token set.');
		}
		$Request = OAuthRequest::from_consumer_and_token($this->OAuthConsumer, (($useToken) ? $this->OAuthToken : null), (($file === null) ? 'GET' : 'POST'), $url, $params);
		$Request->sign_request($this->OAuthSignatureMethod, $this->OAuthConsumer, (($useToken) ? $this->OAuthToken : null));
		
		/**
		 * Initialize cURL instance
		 */
		if(!function_exists('curl_init')){
			throw new DropLibException_Curl('cURL not available.');
		}
		$ch = curl_init();
		//echo $Request->to_url().chr(10);
		curl_setopt($ch, CURLOPT_URL, $Request->to_url());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		/**
		 * Disable SSL check if necessary
		 */
		if (!$this->sslCheck){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		
		/**
		 * Set file upload, if necessary
		 */
		if ($file !== null){
			$postdata = array('file' => "@$file");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		}
		
		/**
		 * Execute request and get response status code
		 */
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return array(
			'response' => $response,
			'status' => $status
		);
	
	}
	
}