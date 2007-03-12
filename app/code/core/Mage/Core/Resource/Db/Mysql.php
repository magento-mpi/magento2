<?php

class Mage_Core_Resource_Db_Mysql extends Mage_Core_Resource_Db
{
    public function __construct($config)
    {
        parent::__construct();
        
        $this->setConfig($config);
        
        $this->setConnection(Zend_Db::factory('PDO_MYSQL', (array)$config->connection));
    }

}