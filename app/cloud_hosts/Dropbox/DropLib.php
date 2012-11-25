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

require_once 'DropLib.Exceptions.php';
require_once 'DropLib.Base.php';
require_once 'DropLib.Http.php';
require_once 'oAuth.php';

class DropLib extends DropLib_Base{
	
	/**
	 * API Base URL
	 */
	const API_BASE = 'https://api.dropbox.com/1/';
	
	/**
	 * API Base URL for downloading files
	 */
	const API_CONTENT_BASE = 'https://api-content.dropbox.com/1/';
	
	/**
	 * API Base URL for authorization
	 */
	const API_AUTHORIZATION_BASE = 'https://www.dropbox.com/1/oauth/authorize';
	
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
	 * API language code
	 */
	//protected $locale;
	
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
			'locale' =>				'en',
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
	 * Get current locale
	 *
	 * @return String Current locale (language code)
	 */
	public function getLocale(){
		
		return $this->Http->getLocale();
		
	}
	
	/**
	 * Set API locale
	 *
	 * @param String $newLocale New locale (language code)
	 * @throws DropLibException
	 */
	public function setLocale($newLocale){
		
		$this->Http->setLocale($newLocale);
		
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
	 * (Deprecated) Fetch oAuth token by passing email adress and password
	 */
	public function authorize($email, $password){
		
		throw new DropLibException_Deprecated('The function authorize() is not availabe anymore.');
		
	}
	
	/**
	 * Step 1 of authentication.
	 * Obtain an OAuth request token to be used for the rest of the
	 * authentication process.
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
	 * Step 2 of authentication.
	 * Applications should direct the user to this URL to let the user log in
	 * to Dropbox and choose whether to grant the application the ability to
	 * access files on their behalf.
	 *
	 * Without the user's authorization in this step, it isn't possible for
	 * your application to obtain an access token from accessToken().
	 *
	 * @param String $callback After the user authorizes an application, the user is redirected to the application-served URL provided by this parameter. 
	 * @throws DropLibException
	 */
	public function authorizeUrl($callback = null){
		
		$token = $this->getToken();
		
		if(is_null($token)){
			throw new DropLibException_OAuth('You need to get an request token before generating the authorization url.');
		}
		
		$url = self::API_AUTHORIZATION_BASE . '?oauth_token=' . $token['key'];
		if($this->strParamSet($callback)){
			$url .= '&oauth_callback=' . $callback;
		}
		
		return $url;
	}
	
	/**
	 * Step 3 of authentication.
	 * After step 2 is complete, the application can call accessToken() to
	 * acquire an access token.
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
	 * Retrieves information about the user's account.
	 *
	 * @return Array Associative array. See documentation for examples.
	 */
	public function accountInfo(){
		
		$response = $this->Http->fetch(self::API_BASE . 'account/info');
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * (Deprecated) Create a new DropBox account
	 */
	public function createAccount($firstName, $lastName, $email, $password){
		
		throw new DropLibException_Deprecated('The function createAccount() is not availabe anymore.');
		
	}
	
	/**
	 * Get contents for specified file
	 *
	 * @param String $path The path to the file you want to retrieve.
	 * @param String $revision The revision of the file to retrieve. Defaults to the most recent revision.
	 * @return String Raw file contents
	 * @throws DropLibException
	 */
	public function download($path, $revision = null){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array();
		if($this->strParamSet($revision)){
			$params['rev'] = $revision;
		}
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'files/' . $this->root . '/' . $this->encodePath($path), $params);
		return $this->decodeResponse($response);
		
	}

	/**
	 * ADDED BY SAM RAYNER
	 * Get contents for specified file along with metadata
	 *
	 * @param String $path The path to the file you want to retrieve.
	 * @param String $revision The revision of the file to retrieve. Defaults to the most recent revision.
	 * @return Array Raw file contents and metadata
	 * @throws DropLibException
	 */
	public function getFile($path, $revision = null){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array();
		if($this->strParamSet($revision)){
			$params['rev'] = $revision;
		}
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'files/' . $this->root . '/' . $this->encodePath($path), $params, true);

		$fetched = array(
			"content" => $this->decodeResponse($response),
			"metadata" => array()
		);

		if(isset($reponse["headers"]["x-dropbox-metadata"])) {
			$metadata = array(
				"response" => $reponse["headers"]["x-dropbox-metadata"],
				"status" => $reponse["status"]
			);

			$fetched["metadata"] = $this->decodeResponse($metadata);
		}

		return $fetched;
		
	}
	
	/**
	 * Upload a file to DropBox (max. 300MB)
	 *
	 * @param String $path The path to the folder the file should be uploaded into. This parameter should not point to a file.
	 * @param String $file Absolute(!) local file path.
	 * @param Bool This value determines what happens when there's already a file at the specified path. See documentation.
	 * @return Bool True if upload was successfull
	 * @throws DropLibException
	 */
	public function upload($path, $file, $overwrite = true){
		
		if(!$this->strParamsSet($path, $file)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		if(!$this->validFile($file)){
			throw new DropLibException_InvalidArgument('File doesn\'t exist or not readable.');
		}
		
		$file = $this->correctSlashes($file);
		
		$response = $this->Http->fetch(self::API_CONTENT_BASE . 'files/' . $this->root . '/' . $this->encodePath($path), array(
			'file' => $file,
			'overwrite' => $overwrite
		),true, $file);
		
		return ($response['status'] === 200) ? true : $this->decodeResponse($response);
	}
	
	/**
	 * Retrieves file and folder metadata.
	 *
	 * @param String $path File or folder path, relative to DropBox root
	 * @param Bool $list Include directory listing
	 * @param String $hash If a hash is set, this method simply returns true if nothing has changed since the last request. Good for caching.
	 * @param Int $fileLimit Maximum number of file-information to receive
	 * @return Array Associative array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function metadata($path, $list = true, $hash = null, $fileLimit = 10000, $revision = null, $includeDeleted = false){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array(
			'list' => $list,
			'file_limit' => $fileLimit,
			'include_deleted' => $includeDeleted
		);
		if($this->strParamSet($hash)){
			$params['hash'] = $hash;
		}
		if($this->strParamSet(strval($revision))){
			$params['rev'] = $revision;
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'metadata/' . $this->root . '/' . $this->encodePath($path), $params);
		return ($response['status'] == 304) ? true : $this->decodeResponse($response);
		
	}
	
	/**
	 * Obtains metadata for previous revisions of a file.
	 *
	 * Only revisions up to thirty days old are available. You can use the
	 * revision number in conjunction with the restore() call to revert the
	 * file to its previous state.
	 * 
	 * @param String $path The path to the file.
	 * @param String $limit The service will not report listings containing more than $limit revisions.
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function revisions($path, $limit = 10){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array(
			'rev_limit' => $limit
		);
		
		$response = $this->Http->fetch(self::API_BASE . 'revisions/' . $this->root . '/' . $this->encodePath($path), $params);
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Restores a file path to a previous revision.
	 * 
	 * @param String $path The path to the file.
	 * @param String $revision The revision of the file to restore. 
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */	
	public function restore($path, $revision){
		
		if(!$this->strParamSet($path) or !$this->strParamSet(strval($revision))){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$params = array(
			'rev' => $revision
		);
		
		$response = $this->Http->fetch(self::API_BASE . 'restore/' . $this->root . '/' . $this->encodePath($path), $params);
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Returns metadata for all files and folders that match the search query.
	 *
	 * Searches are limited to the folder path and its sub-folder hierarchy
	 * provided in the call.
	 * 
	 * @param String $path The path to the folder you want to search in.
	 * @param String $query The search string. Must be at least three characters long.
	 * @param Int $fileLimit The service will not report listings containing more than file_limit files.
	 * @param Bool $includeDeleted Include deleted files and folders in the search results.
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */	
	public function search($path, $query, $fileLimit = 10000, $includeDeleted = false){
		
		if(!$this->strParamsSet($path, strval($query))){
			throw new DropLibException_InvalidArgument('Invalid or missing argument(s).');
		}
		
		$params = array(
			'query' => $query,
			'file_limit' => $fileLimit,
			'include_deleted' => $includeDeleted
		);
		
		$response = $this->Http->fetch(self::API_BASE . 'search/' . $this->root . '/' . $this->encodePath($path), $params);
		return $this->decodeResponse($response);
		
	}

	/**
	 * Creates and returns a shareable link to files or folders.
	 *
	 * Note: Links created by the share() API call expire after thirty days.
	 * 
	 * @param String $path The path to the file you want a sharable link to.
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */	
	public function share($path, $short_url=true){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$params = array(
			'short_url' => (int)$short_url
		);
		
		$response = $this->Http->fetch(self::API_BASE . 'shares/' . $this->root . '/' . $this->encodePath($path), $params);
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Returns a link directly to a file.
	 *
	 * Similar to share(). The difference is that this bypasses the Dropbox
	 * webserver, used to provide a preview of the file, so that you can
	 * effectively stream the contents of your media.
	 * 
	 * @param String $path The path to the media file you want a direct link to.
	 * @return Array Associative Array. See documentation for examples.
	 * @throws DropLibException
	 */
	public function media($path){
		
		if(!$this->strParamSet($path)){
			throw new DropLibException_InvalidArgument('Invalid or missing argument.');
		}
		
		$response = $this->Http->fetch(self::API_BASE . 'media/' . $this->root . '/' . $this->encodePath($path));
		return $this->decodeResponse($response);
		
	}
	
	/**
	 * Get a thumbnail of a picture inside the dropbox
	 *
	 * @param String $path Path to image file, relative to DropBox root
	 * @param String $size Thumbnail size. See documentation.
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