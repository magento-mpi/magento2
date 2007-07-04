<?php

class Mage_Core_Model_Resource_Type_Db_Pdo_Mysql extends Mage_Core_Model_Resource_Type_Db
{
    public function getConnection($config)
    {
        $conn = new Varien_Db_Adapter_Pdo_Mysql((array)$config);
        
        if (!empty($config->initStatements) && $conn) {
            $conn->query((string)$config->initStatements);
        }

        return $conn;
    }

}