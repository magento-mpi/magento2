<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Acl model
 *
 * @category   Magento
 * @package    Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Acl extends Zend_Acl
{
    /**
     * All the group roles are prepended by G
     *
     */
    const ROLE_TYPE_GROUP = 'G';

    /**
     * All the user roles are prepended by U
     *
     */
    const ROLE_TYPE_USER = 'U';

    /**
     * User types for store access
     * G - Guest customer (anonymous)
     * C - Authenticated customer
     * A - Authenticated admin user
     *
     */
    const USER_TYPE_GUEST    = 'G';
    const USER_TYPE_CUSTOMER = 'C';
    const USER_TYPE_ADMIN    = 'A';

    /**
     * Permission level to deny access
     *
     */
    const RULE_PERM_DENY = 0;

    /**
     * Permission level to inheric access from parent role
     *
     */
    const RULE_PERM_INHERIT = 1;

    /**
     * Permission level to allow access
     *
     */
    const RULE_PERM_ALLOW = 2;

    /**
     * Get role registry object or create one
     *
     * @return Magento_Api_Model_Acl_Role_Registry
     */
    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = Mage::getModel('Magento_Api_Model_Acl_Role_Registry');
        }
        return $this->_roleRegistry;
    }

    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role $role
     * @param Zend_Acl_Role $parent
     * @return Magento_Api_Model_Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}
