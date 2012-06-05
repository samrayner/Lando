<?php
/**
 * DropLib - DropBox API Class
 *
 * @package DropLib
 * @version 2.1.0
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
	 * API language code
	 */
	protected $locale;

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
		$this->locale = $params['locale'];
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
	 * Set new API locale
	 */
	public function setLocale($newLocale){
		
		if(!$this->strParamSet($newLocale)){
			throw new DropLibException_InvalidArgument('Empty argument or invalid argument type.');
		}
		
		$this->locale = $newLocale;
		
	}
	
	/**
	 * Returns the current API locale
	 */
	public function getLocale(){
		
		return $this->locale;
		
	}
	
	/**
	 * EDIT BY SAM RAYNER TO OPTIONALLY GET HEADERS
	 */
	public function fetch($url, $params = array(), $useToken = true, $file = null, $getHeaders = false){
		
		$defaultParams = array(
			'locale' => $this->locale	
		);
		$params = array_merge($defaultParams, (is_array($params)) ? $params : array());
		
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

		if($getHeaders)
			curl_setopt($ch, CURLOPT_HEADER, true);

		$fetched = array();
		$response = curl_exec($ch);

		if($getHeaders) {
			list($header_text, $response) = explode("\r\n\r\n", $response, 2);
			$headers = $this->list_headers($header_text);

			$http_code = explode(" ", $headers["http_code"]);
			$status = intval($http_code[1]);

			$fetched['headers'] = $headers;
		}
		else {
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		}

		curl_close($ch);

		$fetched['response'] = $response;
		$fetched['status'] = $status;

		return $fetched;
	
	}

	/**
	 * ADDED BY SAM RAYNER TO PARSE RESPONSE HEADERS TO ARRAY
	 */
	private function list_headers($header_text) {
    $headers = array();

    foreach (explode("\r\n", trim($header_text)) as $i => $line) {
    	if ($i === 0)
				$headers['http_code'] = trim($line);
			else {
				list($key, $value) = explode(': ', $line, 2);
				$headers[$key] = trim($value);
      }
		}

		return $headers;
	}
	
}