<?php

/**
 * DropLib
 * DropBox API Class
 *
 * @copyright	(c) 2011 by JonasDoebertin (http://codecanyon.net/user/JonasDoebertin)
 * @author		Jonas Döbertin
 * @package		DropLib
 * @version		Version 1.0
 */

require_once('OAuth.php');

class DropLibException extends Exception{
	
}

class DropLib{
	
	const API_BASE = 'https://api.dropbox.com/0';
	
	const API_CONTENT_BASE = 'https://api-content.dropbox.com/0';
	
	const API_ROOT_DROPBOX = 'dropbox';
	
	const API_ROOT_SANDBOX = 'sandbox';
	
	const API_FUNC_TOKEN = '/token';
	
	const API_FUNC_ACCOUNT_INFO = '/account/info';
	
	const API_FUNC_ACCOUNT = '/account';
	
	const API_FUNC_FILES = '/files';
	
	const API_FUNC_METADATA = '/metadata';
	
	const API_FUNC_THUMBNAIL = '/thumbnails';
	
	const API_FUNC_COPY = '/fileops/copy';
	
	const API_FUNC_CREATE_FOLDER = '/fileops/create_folder';
	
	const API_FUNC_DELETE = '/fileops/delete';
	
	const API_FUNC_MOVE = '/fileops/move';
	
	
	/** OAuth consumer instance */
	protected $OAuthConsumer;
	
	/** OAuth token instance */
	protected $OAuthToken;
	
	/** OAuth Signature Method instance */
	protected $OAuthSignatureMethod;
	
	/** Dropbox root */
	protected $root = self::API_ROOT_DROPBOX;
	
	/** Use Exceptions */
	protected $useExceptions = true;
	
	/** Disable SSL Check */
	protected $noSSLCheck = false;
	
	/**
	* Constructor function for all new DropLib instances
	*
	* @param String $consumerKey Your consumer key
	* @param String $consumerSecret Your consumer secret
	* @param String $tokenKey The users token key (optional)
	* @param String $tokenSecret The users token secret (optional)
	* @return DropLib New DropLib instance
	*/	
	public function __construct($consumerKey, $consumerSecret, $tokenKey = null, $tokenSecret = null){
		$this->OAuthConsumer = new OAuthConsumer($consumerKey, $consumerSecret);
		if (($tokenKey !== null) and ($tokenSecret !== null)){
			$this->OAuthToken = new OAuthToken($tokenKey, $tokenSecret);
		}
		$this->OAuthSignatureMethod = new OAuthSignatureMethod_HMAC_SHA1;
	}
	
	/**
	* Is sandbox mode enabled?
	*
	* @return Boolean
	*/
	public function getUseSandbox(){
		return ($this->root == self::API_ROOT_SANDBOX) ? true : false;
	}
	
	/**
	* Enable/disable sandbox mode
	*
	* @param Boolean $useSandbox (optional, default is true)
	*/
	public function setUseSandbox($useSandbox = true){
		$this->root = ($useSandbox) ? self::API_ROOT_SANDBOX : self::API_ROOT_DROPBOX;
	}
	
	/**
	* Is Exception raising enabled?
	*
	* @return Boolean
	*/
	public function getUseExceptions(){
		return $this->useExceptions;
	}
	
	/**
	* Enable/disable exception raising
	*
	* @param Boolean $useExceptions (optional, default is true)
	*/
	public function setUseExceptions($useExceptions = true){
		$this->useExceptions = $useExceptions;
	}
	
	/**
	* Is SSL check disabled?
	*
	* @return Boolean
	*/
	public function getNoSSLCheck(){
		return $this->noSSLCheck;
	}
	
	/**
	* Enable/disable SSL certificate check
	*
	* @param Boolean $noSSLCheck (optional, default is true)
	*/
	public function setNoSSLCheck($noSSLCheck = true){
		$this->noSSLCheck = $noSSLCheck;
	}
	
	/**
	* Get the currently used user token
	*
	* Return associative array if token exists, otherwise returns false
	*
	* @return Mixed
	*/
	public function getToken(){
		return (is_object($this->OAuthToken)) ? array('key' => $this->OAuthToken->key, 'secret' => $this->OAuthToken->secret) : false;
	}
	
	/**
	* Authorize user specified by email and password
	*
	* @param String $email
	* @param String $password
	* @return Array Associative array with token key and secret
	*/
	public function authorize($email, $password){
		$url = self::API_BASE . self::API_FUNC_TOKEN;
		$result = $this->request($url, array('email' => $email, 'password' => $password), 'GET', true);
		/* store token for later use */
		if (isset($result['token'])){
			$this->OAuthToken = new OAuthToken($result['token'], $result['secret']);
		}
		elseif (isset($result['body']['token'])){
			$this->OAuthToken = new OAuthToken($result['body']['token'], $result['body']['secret']);
		}
		return $result;
	}
	
	/**
	* Get DropBox account info
	*
	* @return Array Associative array
	*/
	public function accountInfo(){
		$url = self::API_BASE . self::API_FUNC_ACCOUNT_INFO;
		return $this->request($url);
	}
	
	/**
	* Create a new DropBox account
	*
	* @param String $firstName
	* @param String $lastName
	* @param String $email
	* @param String $password
	* @return Array Associative array
	*/
	public function createAccount($firstName, $lastName, $email, $password){
		$url = self::API_BASE . self::API_FUNC_ACCOUNT;
		return $this->request($url, array('first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'password' => $password), 'GET', true);	
	}
	
	/**
	* Get file contents
	*
	* @param String $path
	* @return Array Associative array
	*/
	public function download($path){
		$url = self::API_CONTENT_BASE . self::API_FUNC_FILES . '/' . $this->root . '/' . ltrim($path, '/');
		return $this->request($url);
	}
	
	/**
	* Upload file to DropBox
	*
	* @param String $file Local file path
	* @param String $path Destination path
	* @return Array Associative array
	*/
	public function upload($file, $path = ''){
		if (!is_readable($file)){
			throw new DropLibException("Error: File \"$file\" is not readable or doesn't exist.");
		}
		$file = $this->correctSlashes($file);
		$url = self::API_CONTENT_BASE . self::API_FUNC_FILES . '/' . $this->root . '/' . trim($path, '/');
		return $this->request($url, array('file' => $file), 'POST', false, $file);
	}
	
	/**
	* Get metadata for file or directory
	*
	* @param String $path DropBox path
	* @param Boolean $listContents Include directory listing or not
	* @param Int Max files in directory (refer to DropBox API documentation)
	* @return Array Associative array
	*/
	public function metadata($path = '', $listContents = true, $fileLimit = 10000){
		$url = self::API_BASE . self::API_FUNC_METADATA . '/' . $this->root . '/' . ltrim($path, '/');
		return $this->request($url, array('list' => ($listContents)? 'true' : 'false', 'file_limit' => $fileLimit));
	}
	
	/**
	* Get image thumbnail
	*
	* @param String $path Dropbox path
	* @param String $size Thumbnail size (small, medium, large) (optional, default is small)
	* @param String $format Thumbnail format (JPEG, PNG) ( optional, default is JPEG)
	* @param Boolean $raw Return raw data instead of Base64 encoded data (optional, default is false)
	* @return String Thumbnail file contents
	*/
	public function thumbnail($path, $size = 'small', $format = 'JPEG', $raw = false){
		$url = self::API_CONTENT_BASE . self::API_FUNC_THUMBNAIL . '/dropbox/' . ltrim($path, '/');
		$result = $this->request($url, array('size' => $size, 'format' => $format));
		if ($raw){
			return $result;
		}
		else{
			return 'data:image/' . $format . ';base64,' . base64_encode( (isset($result['body'])) ? $result['body'] : (!is_array($result)) ? $result : '' );
		}
	}
	
	/**
	* Copy a DropBox file / directory
	*
	* @param String $from
	* @param String $to
	* @return Array Associative array
	*/
	public function copy($from, $to){
		$url = self::API_BASE . self::API_FUNC_COPY;
		return $this->request($url, array('from_path' => $from, 'to_path' => $to, 'root' => $this->root));
	}
	
	/**
	* Create a new folder
	*
	* @param String $path
	* @return Array Associative array
	*/
	public function createFolder($path){
		$url = self::API_BASE . self::API_FUNC_CREATE_FOLDER;
		return $this->request($url, array('path' => $path, 'root' => $this->root));
	}
	
	/**
	* Delete file / directory
	*
	* @param String $path
	* @return Array Associative array
	*/
	public function delete($path){
		$url = self::API_BASE . self::API_FUNC_DELETE;
		return $this->request($url, array('path' => $path, 'root' => $this->root));
	}
	
	/**
	* Move / rename file or directory
	*
	* @param String $from
	* @param String $to
	* @return Array Associative array
	*/
	public function move($from, $to){
		$url = self::API_BASE . self::API_FUNC_MOVE;
		return $this->request($url, array('from_path' => $from, 'to_path' => $to, 'root' => $this->root));
	}
	
	/**
	* Execute API request via cURL
	*
	* @param String $url
	* @param Array $args Associative array with parameters (optional)
	* @param String $method Request method (optional, default is GET)
	* @param Boolean $looseSigning Use loose signing (optional, default is false)
	* @param String $file Local path to file which will be uploaded (optional)
	* @return Array Associative array
	*/
	protected function request($url, $args = null, $method = 'GET', $looseSigning = false, $file = null){
		$args = (is_array($args)) ? $args : array();
		if (!$this->useExceptions){
			$args['status_in_response'] = 'true';
		}
		
		/* Sign Request*/
		$Request = OAuthRequest::from_consumer_and_token($this->OAuthConsumer, $this->OAuthToken, $method, $url, $args);
		$Request->sign_request($this->OAuthSignatureMethod, $this->OAuthConsumer, (!$looseSigning)? $this->OAuthToken : '');
		
		/* Build cURL Request */
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $Request->to_url());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($this->noSSLCheck){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		}
		
		/* file upload */
		if ($file !== null){
			$data = array('file' => "@$file");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		
		$content = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		if (($status === 200) or (!$this->useExceptions)){
			return $this->formatResult($content);
		}
		else{
			throw new DropLibException("Error: DropBox API returned status \"$status\". Refer to \"https://www.dropbox.com/developers/docs\" for further information. ");
		}
	}
	
	/**
	* Change backslashes (\) to normal slashes (/)
	*
	* @param String $string
	* @return String
	*/
	protected function correctSlashes($string){
		return preg_replace("/\\\\/", "/", $string);
	}
	
	/**
	* Formats request result
	*
	* @param String $input JSON string
	* @return Array Associative array
	*/
	protected function formatResult($input){
		$output = json_decode($input, true);
		if (!$this->useExceptions and isset($output['body']) and !is_array($output['body'])){
			$output['body'] = json_decode($output['body'], true);
		}
		return $output;
	}
}
