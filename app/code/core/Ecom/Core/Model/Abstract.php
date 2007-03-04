<?php

abstract class Ecom_Core_Model_Abstract
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
        return Ecom_Core_Resource::getResource($resourceName)->getConnection();
    }

    protected function _getEntity($resourceName, $entityName)
    {
        return Ecom_Core_Resource::getResource($resourceName)->getEntity($entityName);
    }
}