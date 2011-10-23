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

class DropLib_Base{
	
	/**
	 * Checks if a variable is set (not empty) and of string type
	 */
	protected function StrParamSet($strParam){
		
		return isset($strParam) and !empty($strParam) and is_string($strParam);
		
	}
	
	/**
	 * Checks if all passed variables are set (not empty) and of string type
	 */
	protected function strParamsSet(){
		
		$result = true;
		foreach(func_get_args() as $param){
			$result = $result and (isset($param) and !empty($param) and is_string($param));
		}
		return $result;
		
	}
	
	/**
	 * Checks if a file exists and is readable
	 */
	protected function validFile($file){
		
		return is_readable($file);
		
	}
	
	/**
	 * Changes double-backslashes (\\) to slashes (/). Necessary on some windows systems.
	 */
	protected function correctSlashes($file){
		return preg_replace("/\\\\/", "/", $file);
	}
	
	/**
	 * Encode a path parameter the way DropBox likes it.
	 */
	protected function encodePath($path){
		
		return ltrim(str_replace(array('%2F','~'), array('/','%7E'), rawurlencode($path)), '/');
		
	}
	
	/**
	 * Decodes an API response.
	 *
	 * Throws an Exception, if the request was not successfull.
	 *
	 * @throws DropLibException
	 */
	protected function decodeResponse($response){
		
		/* try to decode json */
		$result = json_decode($response['response'], true);
		
		/* if deconding was successfull use decoded array, else use raw response */
		$result = ((!is_null($result)) ? $result : $response['response']);
		
		if($response['status'] !== 200){
			$error = (isset($result['error'])) ? $result['error'] : 'Unknown API error. Check status code (error code)!';
			throw new DropLibException_API($error, $response['status']);
		}
		
		return $result;
		
	}
	
}