<?php



class Mage_Auth_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct($data = array())
    {
        $this->_read = $this->_getConnection('auth_read');
        $this->_write = $this->_getConnection('auth_write');
    }

}