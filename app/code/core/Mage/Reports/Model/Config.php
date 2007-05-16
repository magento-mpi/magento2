<?php

/**
 * Configuration for reports
 *
 * @package    Mage
 * @subpackage Reports
 * @author     Ivan Chepurnyi <mitch@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

 class Mage_Reports_Model_Config extends Varien_Object 
 {
	public function getGlobalConfig( )
	{
		$dom = new DOMDocument();
		$dom -> load( Mage::getBaseDir('etc','Mage_Reports').DS.'flexConfig.xml' );
		
		$baseUrl = $dom -> createElement('baseUrl');
		$baseUrl -> nodeValue = Mage::getBaseUrl();
		
		$dom -> documentElement -> appendChild( $baseUrl );
		
		return $dom -> saveXML();
	}
	
	public function getLanguage( )
	{
		return file_get_contents( Mage::getBaseDir('etc','Mage_Reports').DS.'flexLanguage.xml' );
	}
	
	public function getDashboard( )
	{
		return file_get_contents( Mage::getBaseDir('etc','Mage_Reports').DS.'flexDashboard.xml' );
	} 
 }
 