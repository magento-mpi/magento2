<?php
/**
 * Magento Authorization component. Can be used to add authorization facility to any application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento;

class Authorization implements \Magento\AuthorizationInterface
{
    /**
     * ACL policy
     *
     * @var \Magento\Authorization\Policy
     */
    protected $_aclPolicy;

    /**
     * ACL role locator
     *
     * @var \Magento\Authorization\RoleLocator
     */
    protected $_aclRoleLocator;

    /**
     * @param \Magento\Authorization\Policy $aclPolicy
     * @param \Magento\Authorization\RoleLocator $roleLocator
     */
    public function __construct(
        \Magento\Authorization\Policy $aclPolicy,
        \Magento\Authorization\RoleLocator $roleLocator
    ) {
        $this->_aclPolicy = $aclPolicy;
        $this->_aclRoleLocator = $roleLocator;
    }

    /**
     * Check current user permission on resource and privilege
     *
     * @param   string $resource
     * @param   string $privilege
     * @return  boolean
     */
    public function isAllowed($resource, $privilege = null)
    {
        return $this->_aclPolicy->isAllowed($this->_aclRoleLocator->getAclRoleId(), $resource, $privilege);
    }
}
