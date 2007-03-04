<?php

#include_once 'Ecom/Core/Model/Db.php';

class Ecom_Sales_Model_Mysql4 extends Ecom_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();
        
        $this->_read = $this->_getConnection('sales_read');
        $this->_write = $this->_getConnection('sales_write');
    }

}