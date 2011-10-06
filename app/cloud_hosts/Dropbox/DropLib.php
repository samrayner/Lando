<?php
/**
 * DropLib - DropBox API Class
 *
 * @package DropLib
 * @version 2.0.0
 * @copyright Copyright 2011 by Jonas Doebertin. All rights reserved.
 * @author Jonas Doebertin
 * @license Sold exclusively on CodeCanyon
 */

require_once 'DropLib.Exceptions.php';
require_once 'DropLib.Base.php';
require_once 'DropLib.Http.php';
require_once 'oAuth.php';

class DropLib extends DropLib_Base{
	
	/**
	 * API Base URL
	 */
	const API_BASE = 'https://api.dropbox.com/0/';
	
	/**
	 * API Base URL for downloading files
	 */
	const API_CONTENT_BASE = 'https://api-content.dropbox.com/0/';
	
	/**
	 * Root folder for full mode
	 */
	const API_ROOT_DROPBOX = 'dropbox';
	
	/**
	 * Root folder for sandbox mode
	 */
	const API_ROOT_SANDBOX = 'sandbox';
	
	/**
	 * DropLib_Http Object
	 */
	protected $Http;
	
	/**
	 * Default DropBox root directory
	 */
	protected $root = self::API_ROOT_DROPBOX;
	
	
	/**
	 * Constructor
	 *
	 * @param Array $params Array with DropLib configuration. See constructor code for possible values.
	 * @throws DropLibException
	 */
	public function __construct($params = null){
		
		$params = (is_array($params)) ? $params : array();
		$defaultParams = array(
			
			'sslCheck' =>			true,
			'consumerKey' =>		null,
			'consumerSecret' =>		null,
			'tokenKey' =>			null,
			'tokenSecret' =>		null,
			'rootDirectory' =>		self::API_ROOT_DROPBOX
		
		);
		$params = array_merge($defaultParams, $params);
		
		if(!$this->strParamsSet($params['consumerKey'], $params['consumerSecret'])){
			throw new DropLibException_InvalidArgument('No consumer token found.');;
		}
		
		$this->Http = new DropLib_Http($params);
		$this->sslCheck = $params['sslCheck'];
		
	}
	
	/**
	 * Get current DropBox root folder
	 *
	 * @return String Current DropBox root folder
	 */
	public function getRoot(){
		
		return $this->root;
		
	}
	
	/**
	 * Set DropBox root folder
	 *
	 * @param String $newRoot New root folder. Either "dropbox" or "sandbox".
	 * @throws DropLibException
	 */
	public function setRoot($newRoot){
		
		if(in_array(mb_strtolower($newRoot), array('dropbox', 'sandbox'))){
			$this->root = mb_strtolower($newRoot);
		} else{
			throw new DropLibException_InvalidArgument('Invalid argument value.');
		}
		
	}
	
	/**
	 * Get current SSL check state
	 *
	 * @return Bool SSL check state
	 */
	public function getSslCheck(){
		
		return $this->sslCheck;
		
	}
	
	/**
	 * Set SSL check state
	 *
	 * @param Bool $newSslCheck New SSL check state
	 * @throws DropLibException
	 */
	public function setSslCheck($newSslCheck){
		
		if(is_bool($newSslCheck)){
			$this->sslCheck = $newSslCheck;
		} else{
			throw new DropLibException_InvalidArgument('Invalid argument type.');
		}
		
	}
	
	/**
	 * Get current oAuth token
	 *
	 * @return Array Current oAuth token. Array(key, secret).
	 */
	public function getToken(){
		
		return $this->Http->getToken();
		
	}
	
	/**
	 * Fetch oAuth token by passing email adress and password
	 *
	 * Use this to bypass the usual oAuth authorization procedure. {@link https://www.dropbox.com/developers/web_docs#authentication-for-web}
	 *
	 * @param String $email Users email address
	 * @param String $password Users password
	 * @return Array Current oAuth token. Array(key, secret).
	 * @throws DropLibException
	 */
	public function authorize($email, $password){
		
		if(!$this->strParamsSet($email, $password)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'token', array(
			'email' => $email,
			'password' => $password	
		), false);
		
		$data = $this->decodeResponse($response);
		$this->Http->setToken($data['token'], $data['secret']);
		
		return $this->Http->getToken();
		
	}
	
	/**
	 * Fetch the oAuth request token
	 *
	 * @return Array oAuth request token. Array(key, secret).
	 * @throws DropLibException
	 */
	public function requestToken(){
		
		$response = $this->Http->fetch(self::API_BASE . 'oauth/request_token', null, false);
		
		if($response['status'] === 200){
			$token = array();
			parse_str($response['response'], $token);
			$this->Http->setToken($token['oauth_token'], $token['oauth_token_secret']);
			return $this->Http->getToken();
		} else{
			throw new DropLibException_OAuth('Unable to retrieve request token.');
		}
		
	}
	
	/**
	 * Get the oAuth authorization URL
	 *
	 * @param String $callback Callback URL. After successfull authorization, DropBox will redirect the user to this URL
	 * @throws DropLibException
	 */
	public function authorizeUrl($callback = null){
		
		$token = $this->getToken();
		
		if(is_null($token)){
			throw new DropLibException_OAuth('You need to get an request token before generating the authorization url.');
		}
		
		$url = 'https://www.dropbox.com/0/oauth/authorize?oauth_token=' . $token['key'];
		if($this->strParamSet($callback)){
			$url .= '&oauth_callback=' . $callback;
		}
		
		return $url;
	}
	
	/**
	 * Fetch the oAuth access token
	 *
	 * @return Array oAuth access token. Array(key, secret).
	 * @throws DropLibException
	 */
	public function accessToken(){
		
		$response = $this->Http->fetch(self::API_BASE . 'oauth/access_token');
		
		if($response['status'] === 200){
			$token = array();
			parse_str($response['response'], $token);
			$this->Http->setToken($token['oauth_token'], $token['oauth_token_secret']);
			return $this->Http->getToken();
		} else{
			throw new DropLibException_OAuth('Unable to retrieve access token.');
		}
	}
	
	/**
	 * Get various DropBox account infos
	 *
	 * @return Array Associative array. See documentation for examples.
	 */
	public function accountInfo(){
		
		$response = $this->Http->fetch(self::API_BASE . 'account/info');
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Create a new DropBox account
	 *
	 * @param String $firstName Users first name
	 * @param String $lastName Users last name
	 * @param String $email Users email address
	 * @param String $password Users password
	 * @return Array Associative array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function createAccount($firstName, $lastName, $email, $password){
		
		if(!$this->strParamsSet($firstName, $lastName, $email, $password)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'account', array(
			'first_name' => $firstName,
			'last_name' => $lastName,
			'email' => $email,
			'password' => $password	
		));	
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Get contents for specified file
	 *
	 * @param String $path Full file path, relative to DropBox root directory.
	 * @return String Raw file contents
	 * @throws DropLibException
	 */
	public function download($path){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'files/' . $this->root . '/' . $this->encodePath($path));
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Upload a file to DropBox (max. 300MB)
	 *
	 * @param String $path Target path, relative to DropBox root (excluding filename).
	 * @param String $file Absolute(!) local file path.
	 * @return Bool True, if upload was successfull
	 * @throws DropLibException
	 */
	public function upload($path, $file){
		
		if(!$this->strParamsSet($path, $file)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		if(!$this->validFile($file)){
			throw new DropLibException_InvalidArgument('File doesn\'t exist or not readable.');
		}
		
		$file = $this->correctSlashes($file);
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'files/' . $this->root . '/' . $this->encodePath($path), array(
			'file' => $file
		),true, $file);
		
		return ($response['status'] === 200) ? true : $this->decodeResponse($response);
	}
	
	/**
	 * Get metadata for specified file or directory
	 *
	 * @param String $path File or folder path, relative to DropBox root
	 * @param Bool $list Include directory listing
	 * @param String $hash If a hash is set, this method simply returns true if nothing has changed since the last request. Good for caching.
	 * @param Int $fileLimit Maximum number of file-information to receive
	 * @return Array Associative array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function metadata($path, $list = true, $hash = null, $fileLimit = 10000){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array(
			'list' => $list,
			'file_limit' => $fileLimit	
		);
		if($this->strParamSet($hash)){
			$params['hash'] = $hash;
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'metadata/' . $this->root . '/' . $this->encodePath($path), $params);
		return ($response['status'] == 304) ? true : $this->decodeResponse($response);
		
	}
	
	/**
	 * Get a thumbnail of a picture inside the dropbox
	 *
	 * @param String $path Path to image file, relative to DropBox root
	 * @param String $size Thumbnail size, possible values are: small, medium and large
	 * @param String $format Thumbnail file format, either JPEG or PNG
	 * @return String Base64 representation of the thumbnail file
	 * @throws DropLibException
	 */
	public function thumbnail($path, $size = 'small', $format = 'JPEG'){
		
		if(!$this->strParamSet($path) or
		   !in_array(strtolower($format), array('jpeg', 'png'))){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$params = array(
			'size' => $size,
			'format' => $format	
		);
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'thumbnails/' . $this->root . '/' . $this->encodePath($path), $params);
		
		return base64_encode($this->decodeResponse($response));
		
	}
	
	/**
	 * Copy a file or directory
	 *
	 * @param String $from Path to source, relative to DropBox root
	 * @param String $to Path to destination, relative to DropBox root
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function copy($from, $to){
		
		if(!$this->strParamsSet($from, $to)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'fileops/copy', array(
			'from_path' => $from,
			'to_path' => $to,
			'root' => $this->root	
		));
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Create a new folder inside of the DropBox
	 *
	 * @param String $path Path of new folder, relative to DropBox root.
	 * @return Array Associative array. See documentation for examples.
	 * @throws DropLibException 
	 */
	public function createFolder($path){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'fileops/create_folder', array(
			'path' => $path,
			'root' => $this->root	
		));
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Delete a folder or file from the DropBox
	 *
	 * @param String $path Path of folder or file, relative to DropBox root.
	 * @return Array Associative array. See documentation for examples.
	 * @throws DropLibException 
	 */
	public function delete($path){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'fileops/delete', array(
			'path' => $path,
			'root' => $this->root	
		));
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Move a file or directory
	 *
	 * @param String $from Path to source, relative to DropBox root
	 * @param String $to Path to destination, relative to DropBox root
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function move($from, $to){
		
		if(!$this->strParamsSet($from, $to)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'fileops/move', array(
			'from_path' => $from,
			'to_path' => $to,
			'root' => $this->root	
		));
		return $this->decodeResponse($response);
		
	}
	
}