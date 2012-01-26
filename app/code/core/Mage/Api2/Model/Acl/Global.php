<?php

class Mage_Api2_Model_Acl_Global
{
    /**
     * Check if the operation is allowed on resources of given type type for given user type/role
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @param string $resourceType
     * @param string $operation
     * @return boolean
     * @throws Mage_Api2_Exception
     */
    public function isAllowed(Mage_Api2_Model_Auth_User_Abstract $apiUser, $resourceType, $operation)
    {
        return Mage_Api2_Model_Acl::getInstance()->isAllowed($apiUser->getRole(), $resourceType, $operation);
    }
}
