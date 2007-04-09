<?php

class Mage_Shiptable_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();
        
        $this->_read = $this->_getConnection('shiptable_read');
        $this->_write = $this->_getConnection('shiptable_write');
    }

}