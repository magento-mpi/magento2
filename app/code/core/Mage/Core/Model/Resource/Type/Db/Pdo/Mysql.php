<?php

class Mage_Core_Model_Resource_Type_Db_Pdo_Mysql extends Mage_Core_Model_Resource_Type_Db
{
    public function getConnection($config)
    {
		$conn = Zend_Db::factory('PDO_MYSQL', (array)$config);

    	return $conn;
    }

}