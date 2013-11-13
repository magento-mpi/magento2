<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserContext;
use Magento\Acl\Builder as AclBuilder;
use Magento\Acl;
use Magento\Core\Model\Config\Cache\Exception;
use Magento\User\Model\RoleFactory;
use Magento\User\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;
use Magento\User\Model\RulesFactory;
use Magento\User\Model\Role;
use Magento\Service\ResourceNotFoundException;

/**
 * Authorization service.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AuthorizationV1 implements AuthorizationV1Interface
{
    /** @var Acl */
    protected $_acl;

    /** @var UserContext */
    protected $_userContext;

    /** @var RoleFactory */
    protected $_roleFactory;

    /** @var RoleCollectionFactory */
    protected $_roleCollectionFactory;

    /** @var RulesFactory */
    protected $_rulesFactory;

    /**
     * @param AclBuilder $aclBuilder
     * @param UserContext $userContext
     * @param RoleFactory $roleFactory
     * @param RulesFactory $rulesFactory
     */
    public function __construct(
        AclBuilder $aclBuilder,
        UserContext $userContext,
        RoleFactory $roleFactory,
        RulesFactory $rulesFactory
    ) {
        $this->_acl = $aclBuilder->getAcl();
        $this->_userContext = $userContext;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($resource, $userContext = null)
    {
        $userContext = $userContext ? $userContext : $this->_userContext;
        $roleId = $this->_getUserRoleId($userContext);
        $this->_acl->isAllowed($roleId, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function grantPermission($userContext, $resources)
    {
        $userType = $userContext->getUserType();
        switch ($userType) {
            case UserContext::USER_TYPE_ADMIN:
                // TODO: Should be implemented if current approach is accepted
                break;
            case UserContext::USER_TYPE_INTEGRATION:
                $roleName = $userContext->getUserType() . $userContext->getUserId();
                $role = $this->createRole($roleName, $userContext, $resources);
                $role->setUserId($userContext->getUserId())->setUserType($userContext->getUserType());
                $role->save();
                break;
            case UserContext::USER_TYPE_CUSTOMER:
                /** Break is intentionally omitted. */
            case UserContext::USER_TYPE_GUEST:
                throw new \LogicException("Users of type '{$userType}' must not be given any permissions explicitly.");
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createRole($roleName, $userContext, $resources)
    {
        $role = $this->_roleFactory->create();
        $userType = $userContext->getUserType();
        switch ($userType) {
            case UserContext::USER_TYPE_ADMIN:
                // TODO: Should be implemented if current approach is accepted
                throw new Exception("Not implemented yet.");
                break;
            case UserContext::USER_TYPE_INTEGRATION:
                $roleType = 'U';
                $parentId = 0;
                $userId = $userContext->getUserId();
                break;
            case UserContext::USER_TYPE_CUSTOMER:
                /** Break is intentionally omitted. */
            case UserContext::USER_TYPE_GUEST:
                $roleType = 'U';
                $parentId = 0;
                $userId = 0;
                $roleCollection = $this->_roleCollectionFactory->create()->setUserFilter($userId, $userType);
                if ($roleCollection->count()) {
                    throw new \LogicException("There should be not more than one role for '{$userType}' user type.");
                }
                break;
            default:
                throw new \LogicException("Unknown user type: '{$userType}'.");
        }
        $role->setRoleName($roleName)
            ->setUserType($userType)
            ->setUserId($userId)
            ->setRoleType($roleType)
            ->setParentId($parentId)
            ->save();
        /** Save resources allowed for users with current role */
        /** @var \Magento\User\Model\Rules $rules */
        $rules = $this->_rulesFactory->create();
        $rules->setRoleId($role->getId())->setResources($resources)->saveRel();
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole($roleId)
    {
        $role = $this->_roleFactory->create();
        $role->load($roleId);
        if (!$role->getId()) {
            throw new ResourceNotFoundException(__('Role with ID "%1" not found.', $roleId));
        }
        /** TODO: \Magento\User\Model\Role is returned and it is incompatible with \Zend_Acl_Role_Interface */
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roleCollection = $this->_roleCollectionFactory->create();
        /** TODO: \Magento\User\Model\Role[] is returned and it is incompatible with \Zend_Acl_Role_Interface[] */
        return $roleCollection->getItems();
    }

    /**
     * Identify user role from user context.
     *
     * @param UserContext $userContext
     * @return string Role identifier in the format compatible with ACL object.
     * @throws ResourceNotFoundException
     */
    protected function _getUserRoleId($userContext)
    {
        $roleCollection = $this->_roleCollectionFactory->create();
        /** @var Role $role */
        $role = $roleCollection->setUserFilter($userContext->getUserId(), $userContext->getUserType())->getFirstItem();
        if ($role->getId()) {
            /** TODO: Refactor this rule, which is defined by \Magento\User\Model\Acl\Loader\Role::populateAcl() */
            return $role->getRoleType() . $role->getUserId();
        } else {
            throw new ResourceNotFoundException(
                __(
                    'Role for user with ID "%1" and user type "%2" not found.',
                    $userContext->getUserId(),
                    $userContext->getUserType()
                )
            );
        }
    }
}
