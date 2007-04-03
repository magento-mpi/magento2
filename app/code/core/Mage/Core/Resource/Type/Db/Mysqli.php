<?php

/**
 * Mysqi Resource
 * 
 * TODO: Write all functions (fetchOne, insert ...)
 *
 * @package    Mage
 * @module     Core
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Resource_Type_Db_Mysqli extends Mage_Core_Resource_Type_Db
{
	public function getConnection($config)
	{
	    $conn = new Mage_Core_Resource_Type_Db_Mysql_Setup($config);
	    
		return $conn;
	}
}