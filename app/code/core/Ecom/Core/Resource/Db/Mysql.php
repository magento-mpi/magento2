<?php

#include_once 'Ecom/Core/Resource/Db.php';
#include_once 'Zend/Db.php';

class Ecom_Core_Resource_Db_Mysql extends Ecom_Core_Resource_Db
{
    public function __construct($config)
    {
        parent::__construct();
        
        $this->setConfig($config);
        
        $this->setConnection(Zend_Db::factory('PDO_MYSQL', $config));
    }

}