<?php

/**
 * Model  for flex reports
 *
 * @package    Mage
 * @subpackage Reports
 * @author     Ivan Chepurnyi <mitch@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */

 class Mage_Reports_Model_Test extends Varien_Object 
 {
	
	public function getUsersCountries( )
	{
		return file_get_contents( Mage::getModuleDir('etc','Mage_Reports').DS.'flexTestDataCountries.xml' );
	}
	
	public function getUsersCities( $countryId )
	{
		$dom = new DOMDocument();
		$dom -> preserveWhiteSpace = false;
		$dom -> load( Mage::getModuleDir('etc','Mage_Reports').DS.'flexTestDataCities.xml' );
		
		$root = $dom -> documentElement;
		$rows = $root -> getElementsByTagName( 'row' );
		
		$childsToRemove = array();
		for( $i = 0; $i < $rows -> length; $i++)
		{
			for( $j = 0; $j < $rows -> item($i) -> childNodes -> length; $j ++ )
				if(
					$rows -> item($i) -> childNodes -> item($j) -> nodeType == XML_ELEMENT_NODE 
						&&
					$rows -> item($i) -> childNodes -> item($j) -> nodeName == 'countryId'
						&& 
					$rows -> item($i) -> childNodes -> item($j) -> nodeValue != $countryId
				)
					$childsToRemove[] = $rows -> item($i);
		}
		
		foreach( $childsToRemove as $child )
			$root -> removeChild( $child );
		
		return $dom -> saveXML();
	}
	
	
 }
 