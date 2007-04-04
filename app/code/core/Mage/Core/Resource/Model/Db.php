<?php

abstract class Mage_Core_Resource_Model_Db extends Mage_Core_Resource_Model_Abstract
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
        return (string)$this->_getEntity($resourceName, $entityName)->table;
    }

    public function getTableName($resourceName, $entityName)
    {
        return $this->_getTableName($resourceName, $entityName);
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