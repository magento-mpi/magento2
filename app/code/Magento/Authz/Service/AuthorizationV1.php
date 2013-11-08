<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserContextInterface;
use Magento\Acl\Builder as AclBuilder;
use Magento\Acl;

/**
 * Authorization service.
 */
class AuthorizationV1 implements AuthorizationV1Interface
{
    /** @var Acl */
    protected $_acl;

    /** @var UserContextInterface */
    protected $_userContext;

    /**
     * @param AclBuilder $aclBuilder
     * @param UserContextInterface $userContext
     */
    public function __construct(AclBuilder $aclBuilder, UserContextInterface $userContext)
    {
        $this->_acl = $aclBuilder->getAcl();
        $this->_userContext = $userContext;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($resource, $userContext = null)
    {
        $userContext = $userContext ? $userContext : $this->_userContext;
        $role = $this->_getUserRole($userContext);
        $this->_acl->isAllowed($role, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function grantPermission($userContext, $resources)
    {
        switch ($userContext->getUserType()) {
            case UserContextInterface::USER_TYPE_ADMIN:
                // TODO: Implement
                break;
            case UserContextInterface::USER_TYPE_CUSTOMER:
                // TODO: Implement
                break;
            case UserContextInterface::USER_TYPE_GUEST:
                // TODO: Implement
                break;
            case UserContextInterface::USER_TYPE_INTEGRATION:
                // TODO: Implement
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createRole($roleName, $resources)
    {
        // TODO: Implement createRole() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($roleId)
    {
        // TODO: Implement getRole() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    /**
     * Identify user role from user context.
     *
     * @param UserContextInterface $userContext
     * @return string
     */
    protected function _getUserRole($userContext)
    {
        // TODO: Implement
        return '';
    }
}
