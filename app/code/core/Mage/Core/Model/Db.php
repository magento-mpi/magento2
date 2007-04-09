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

    public function getReadConnection()
    {
        return $this->_read;
    }

    public function getWriteConnection()
    {
        return $this->_write;
    }
}