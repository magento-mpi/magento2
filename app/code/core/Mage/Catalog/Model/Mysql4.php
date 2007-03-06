<?php



class Mage_Catalog_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();

        $this->_read = $this->_getConnection('catalog_read');
        $this->_write = $this->_getConnection('catalog_write');
    }

}