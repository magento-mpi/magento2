<?php
/**
 * ACL. Can be queried for relations between roles and resources.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class Acl extends \Zend_Acl
{
    /**
     * Permission level to deny access
     */
    const RULE_PERM_DENY = 0;

    /**
     * Permission level to inherit access from parent role
     */
    const RULE_PERM_INHERIT = 1;

    /**
     * Permission level to allow access
     */
    const RULE_PERM_ALLOW = 2;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_roleRegistry = new \Magento\Acl\Role\Registry();
    }

    /**
     * Add parent to role object
     *
     * @param \Zend_Acl_Role $role
     * @param \Zend_Acl_Role $parent
     * @return \Magento\Acl
     */
    public function addRoleParent($role, $parent)
    {
        $this->_getRoleRegistry()->addParent($role, $parent);
        return $this;
    }
}
