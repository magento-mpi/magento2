<?php

abstract class Mage_Core_Model_Abstract
{
    public function __construct()
    {

    }

    /**
     * Enter description here...
     *
     * @param unknown_type $resourceName
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _getConnection($resourceName)
    {
        return Mage::getConfig()->getResource($resourceName)->getConnection();
    }

    protected function _getEntity($resourceName, $entityName)
    {
        return Mage::getConfig()->getResource($resourceName)->getEntity($entityName);
    }
}