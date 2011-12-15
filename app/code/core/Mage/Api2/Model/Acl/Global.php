<?php

class Mage_Api2_Model_Acl_Global
{
    /**
     * Check if the operation is allowed on resources of given type type for given user type/role
     *
     * @param string $accessKey
     * @param string $resourceType
     * @param string $operation
     * @return bool
     * @throws Mage_Api2_Exception
     */
    public function isAllowed($accessKey, $resourceType, $operation)
    {
        $user = new Mage_OAuth_Model_User($accessKey);
        $userType = $user->getType('admin');

        switch ($userType) {
            case Mage_Api2_Model_Auth::USER_TYPE_ADMIN:
                $isAllowed = $this->isAdminAllowed($resourceType, $operation, $accessKey);
            break;

            case Mage_Api2_Model_Auth::USER_TYPE_CUSTOMER:
                $isAllowed = true;
            break;

            case Mage_Api2_Model_Auth::USER_TYPE_GUEST:
                $isAllowed = $this->isGuestAllowed($resourceType, $operation);
            break;

            default:
                throw new Mage_Api2_Exception(sprintf('Invalid user type "%s"', $userType), 403);
        }

        return $isAllowed;
    }

    /**
     * Check if the operation is allowed on resources of given type type for given admin role
     *
     * @param string $resourceType
     * @param string $operation
     * @param string $accessKey
     * @return bool
     */
    protected function isAdminAllowed($resourceType, $operation, $accessKey)
    {
        $user = new Mage_OAuth_Model_User($accessKey);
        $acl = new Mage_OAuth_Model_Acl;

        $role = $user->getRole();
        $isAllowed = $acl->isAllowed($role, $resourceType, $operation);

        return $isAllowed;
    }

    /**
     * Check if the operation is allowed on resources of given type type for given guest user type
     *
     * @param string $resourceType
     * @param string $operation
     * @return bool
     */
    protected function isGuestAllowed($resourceType, $operation)
    {
        $role = Mage_Api2_Model_Auth::USER_TYPE_GUEST;
        $acl = new Mage_OAuth_Model_Acl;
        
        $isAllowed = $acl->isAllowed($role, $resourceType, $operation);
        
        return $isAllowed;
    }
}
