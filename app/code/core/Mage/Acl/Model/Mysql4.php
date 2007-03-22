<?php



class Mage_Acl_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();

        $this->_read = $this->_getConnection('acl_read');
        $this->_write = $this->_getConnection('acl_write');
    }

}