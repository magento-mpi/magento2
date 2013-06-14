<?php
/**
 * Magento Authorization component. Can be used to add authorization facility to any application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Authorization implements Magento_AuthorizationInterface
{
    /**
     * ACL policy
     *
     * @var Magento_Authorization_Policy
     */
    protected $_aclPolicy;

    /**
     * ACL role locator
     *
     * @var Magento_Authorization_RoleLocator
     */
    protected $_aclRoleLocator;

    /**
     * @param Magento_Authorization_Policy $aclPolicy
     * @param Magento_Authorization_RoleLocator $roleLocator
     */
    public function __construct(
        Magento_Authorization_Policy $aclPolicy,
        Magento_Authorization_RoleLocator $roleLocator
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
