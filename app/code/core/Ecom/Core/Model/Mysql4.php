<?php

#include_once 'Ecom/Core/Model/Db.php';

class Ecom_Core_Model_Mysql4 extends Ecom_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();
        
        $this->_read = $this->_getConnection('core_read');
        $this->_write = $this->_getConnection('core_write');
    }

}