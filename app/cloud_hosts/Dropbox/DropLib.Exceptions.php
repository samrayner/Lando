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

/**
 * Generic Exception class
 */
class DropLibException extends Exception{

}

/**
 * Will be thrown if an invalid argument is passed to a DropLib function
 */
class DropLibException_InvalidArgument extends DropLibException{
	
}

/**
 * Will be throw if any oAuth issues occur
 */
class DropLibException_OAuth extends DropLibException{
	
}

/**
 * Will be thrown if cURL is not available
 */
class DropLibException_Curl extends DropLibException{
	
}

/**
 * Will be thrown if the API responds with an error
 */
class DropLibException_API extends DropLibException{
	
}

/**
 * Not used, yet.
 */
class DropLibException_NotImplemented extends DropLibException{
	
}



