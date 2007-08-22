<?php

class Mage_Core_Model_Resource_Type_Db_Pdo_Mysql extends Mage_Core_Model_Resource_Type_Db
{
    public function getConnection($config)
    {
    	$configArr = (array)$config;
    	$configArr['profiler'] = !empty($configArr['profiler']) && $configArr['profiler']!=='false';
    	
        $conn = new Varien_Db_Adapter_Pdo_Mysql($configArr);
        
        if (!empty($configArr['initStatements']) && $conn) {
            $conn->query($configArr['initStatements']);
        }

        return $conn;
    }

}