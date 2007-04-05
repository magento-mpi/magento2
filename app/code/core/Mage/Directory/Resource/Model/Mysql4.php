<?php
class Mage_Directory_Resource_Model_Mysql4 extends Mage_Core_Resource_Model_Db
{
    function __construct()
    {
        parent::__construct();

        $this->_read = $this->_getConnection('directory_read');
        $this->_write = $this->_getConnection('directory_write');
    }

}