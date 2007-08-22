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
    	$configArr = (array)$config;
    	$configArr['profiler'] = !empty($configArr['profiler']) && $configArr['profiler']!=='false';
    	
        $conn = new Varien_Db_Adapter_Mysqli($configArr);
        
        if (!empty($configArr['initStatements']) && $conn) {
            $conn->query($configArr['initStatements']);
        }

        return $conn;
    }
}