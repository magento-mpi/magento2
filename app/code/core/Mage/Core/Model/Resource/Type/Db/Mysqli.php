<?php

/**
 * Mysqi Resource
 * 
 * @package    Mage
 * @module     Core
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Resource_Type_Db_Mysqli extends Mage_Core_Model_Resource_Type_Db
{
	public function getConnection($config)
	{
		$conn = Zend_Db::factory('MYSQLI', (array)$config);
		if (!empty($config->initQuery) && $conn) {
			$conn->query((string)$config->initQuery);
		}

    	return $conn;
	}
}