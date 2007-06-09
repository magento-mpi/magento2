<?php

class Mage_Core_Model_Resource_Type_Db_Mysqli_Setup extends Mage_Core_Model_Resource_Type_Db
{
	public function getConnection($config)
	{
		$conn = Mage::getModel('core/resource_type_db_mysqli_adapter', (array)$config);

    	return $conn;
	}
}