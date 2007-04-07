<?php

class Mage_Core_Resource_Type_Db_Mysqli_Setup extends Mage_Core_Resource_Type_Db
{
	public function getConnection($config)
	{
		$conn = new Mage_Core_Resource_Type_Db_Mysqli_Adapter((array)$config);

    	return $conn;
	}
}