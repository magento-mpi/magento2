<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Acl
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Access Control List. Is used to store and check permissions of users.
 */
class Magento_Acl extends Zend_Acl
{
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

    public function __construct()
    {
        $this->_roleRegistry = new Magento_Acl_Role_Registry();
    }
    
    /**
     * Add parent to role object
     *
     * @param Zend_Acl_Role $role
     * @param Zend_Acl_Role $parent
     * @return Magento_Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}
