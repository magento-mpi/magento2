<?php

class Mage_Catalog_Model_Product_Api2_Rest_Customer_V1 extends Mage_Api2_Model_Resource implements Mage_Api2_Model_Acl_Local_Interface
{
    public function dispatch()
    {
        $request = $this->getRequest();
        $operation = $request->getOperation();
        $resourceType = $request->getResourceType();

        $userId = 'a';
        $resourceId = 'b';

        if (!$this->isAllowed($userId, $resourceId, $operation, false)) {
            throw new Exception(sprintf('Customer (id:%d) has no "%s" permission on resource "%s:%d"', $userId, $operation, $resourceType, $resourceId));
        }

        $this->$operation();
    }

    public function get()
    {
        return $this;
    }

    public function post()
    {
        return $this;
    }

    public function isAllowed($userId, $operation, $resourceId = null, $temp = true)
    {
        $aclManager = new Mage_Api2_Model_Acl_Local;    //no shared object, this rules are independent for each resource
        $isAllowed = $temp;

        return $isAllowed;
    }


}
