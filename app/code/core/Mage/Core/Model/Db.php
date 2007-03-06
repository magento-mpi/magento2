<?php

abstract class Mage_Core_Model_Db extends Mage_Core_Model_Abstract
{
    /**
     * DB read resource
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read = null;

    /**
     * DB write resource
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write = null;

    public function __construct()
    {

    }

    protected function _getTableName($resourceName, $entityName)
    {
        return $this->_getEntity($resourceName, $entityName)->getData('table');
    }

    public function getTableName($resourceName, $entityName)
    {
        return $this->_getEntity($resourceName, $entityName)->getData('table');
    }

    public function getReadConnection()
    {
        return $this->_read;
    }

    public function getWriteConnection()
    {
        return $this->_write;
    }
}