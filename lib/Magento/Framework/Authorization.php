<?php
/**
 * Magento Authorization component. Can be used to add authorization facility to any application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

class Authorization implements \Magento\Framework\AuthorizationInterface
{
    /**
     * ACL policy
     *
     * @var \Magento\Framework\Authorization\Policy
     */
    protected $_aclPolicy;

    /**
     * ACL role locator
     *
     * @var \Magento\Framework\Authorization\RoleLocator
     */
    protected $_aclRoleLocator;

    /**
     * @param \Magento\Framework\Authorization\Policy $aclPolicy
     * @param \Magento\Framework\Authorization\RoleLocator $roleLocator
     */
    public function __construct(
        \Magento\Framework\Authorization\Policy $aclPolicy,
        \Magento\Framework\Authorization\RoleLocator $roleLocator
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
