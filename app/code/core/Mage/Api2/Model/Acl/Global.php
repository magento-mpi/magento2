<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

/**
 * API Global ACL model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
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
        // skip user without role, e.g. Customer
        if (null === $apiUser->getRole()) {
            return true;
        }
        /** @var $aclInstance Mage_Api2_Model_Acl */
        $aclInstance = Mage::getSingleton(
            'Mage_Api2_Model_Acl',
            array('resource_type' => $resourceType, 'operation' => $operation)
        );

        if (!$aclInstance->hasRole($apiUser->getRole())) {
            throw new Mage_Api2_Exception('Role not found', Mage_Api2_Model_Server::HTTP_UNAUTHORIZED);
        }
        if (!$aclInstance->has($resourceType)) {
            throw new Mage_Api2_Exception('Resource not found', Mage_Api2_Model_Server::HTTP_NOT_FOUND);
        }
        return $aclInstance->isAllowed($apiUser->getRole(), $resourceType, $operation);
    }
}
