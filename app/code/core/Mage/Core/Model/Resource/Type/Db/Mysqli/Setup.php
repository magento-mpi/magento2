<?php

class Mage_Core_Model_Resource_Type_Db_Mysqli_Setup extends Mage_Core_Model_Resource_Type_Db
{
	public function getConnection($config)
	{
		$conn = new Varien_Db_Adapter_Mysqli((array)$config);

    	return $conn;
	}
}