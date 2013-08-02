<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for HTTP client classes
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
 
class Magento_HTTP_Client
{

	/**
	 * Disallow to instantiate - pvt constructor
	 */
	private function __construct()
	{
		
	}
	

	/**
	 * Factory for HTTP client
	 * @param string/false $frontend  'curl'/'socket' or false for auto-detect
	 * @return Magento_HTTP_IClient
	 */
	public static function getInstance($frontend = false) 
	{
		if(false === $frontend)  
		{
            $frontend = self::detectFrontend();            			
		}
		if(false === $frontend)
		{ 
			throw new Exception("Cannot find frontend automatically, set it manually");
		}
		
		$class = __CLASS__."_".str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ', $frontend)));
		$obj = new $class();
		return $obj;
	}
	
	/**
	 * Detects frontend type.
	 * Priority is given to CURL
	 * 
	 * @return string/bool
	 */
	protected static function detectFrontend()
	{
	   if(function_exists("curl_init")) {
	   	   return "curl";	   	
	   }
	   if(function_exists("fsockopen")) {
	   	   return "socket";
	   }
	   return false;	   
	}
}