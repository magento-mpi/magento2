<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserIdentifier;
use Magento\Framework\Acl;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\Acl\RootResource as RootAclResource;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Logger;
use Magento\User\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;
use Magento\User\Model\Resource\Rules\CollectionFactory as RulesCollectionFactory;
use Magento\User\Model\Role;
use Magento\User\Model\RoleFactory;
use Magento\User\Model\RulesFactory;

/**
 * Authorization service.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizationV1 implements AuthorizationV1Interface
{
    /**
     * @var AclBuilder
     */
    protected $_aclBuilder;

    /**
     * @var UserIdentifier
     */
    protected $_userIdentifier;

    /**
     * @var RoleFactory
     */
    protected $_roleFactory;

    /**
     * @var RoleCollectionFactory
     */
    protected $_roleCollectionFactory;

    /**
     * @var RulesFactory
     */
    protected $_rulesFactory;

    /**
     * @var RulesCollectionFactory
     */
    protected $_rulesCollectionFactory;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var RootAclResource
     */
    protected $_rootAclResource;

    /**
     * Initialize dependencies.
     *
     * @param AclBuilder $aclBuilder
     * @param UserIdentifier $userIdentifier
     * @param RoleFactory $roleFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param RulesFactory $rulesFactory
     * @param RulesCollectionFactory $rulesCollectionFactory
     * @param Logger $logger
     * @param RootAclResource $rootAclResource
     */
    public function __construct(
        AclBuilder $aclBuilder,
        UserIdentifier $userIdentifier,
        RoleFactory $roleFactory,
        RoleCollectionFactory $roleCollectionFactory,
        RulesFactory $rulesFactory,
        RulesCollectionFactory $rulesCollectionFactory,
        Logger $logger,
        RootAclResource $rootAclResource
    ) {
        $this->_aclBuilder = $aclBuilder;
        $this->_userIdentifier = $userIdentifier;
        $this->_roleFactory = $roleFactory;
        $this->_rulesFactory = $rulesFactory;
        $this->_rulesCollectionFactory = $rulesCollectionFactory;
        $this->_roleCollectionFactory = $roleCollectionFactory;
        $this->_logger = $logger;
        $this->_rootAclResource = $rootAclResource;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed($resources, UserIdentifier $userIdentifier = null)
    {
        $resources = is_array($resources) ? $resources : array($resources);
        $userIdentifier = $userIdentifier ? $userIdentifier : $this->_userIdentifier;
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                throw NoSuchEntityException::doubleField(
                    'userId',
                    $userIdentifier->getUserId(),
                    'userType',
                    $userIdentifier->getUserType()
                );
            }
            foreach ($resources as $resource) {
                if (!$this->_aclBuilder->getAcl()->isAllowed($role->getId(), $resource)) {
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function grantPermissions(UserIdentifier $userIdentifier, array $resources)
    {
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                $role = $this->_createRole($userIdentifier);
            }
            $this->_associateResourcesWithRole($role, $resources);
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new LocalizedException('Error happened while granting permissions. Check exception log for details.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function grantAllPermissions(UserIdentifier $userIdentifier)
    {
        $this->grantPermissions($userIdentifier, array($this->_rootAclResource->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedResources(UserIdentifier $userIdentifier)
    {
        $allowedResources = array();
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                throw new AuthorizationException('The role associated with the specified user cannot be found.');
            }
            $rulesCollection = $this->_rulesCollectionFactory->create();
            $rulesCollection->getByRoles($role->getId())->load();
            $acl = $this->_aclBuilder->getAcl();
            /** @var \Magento\User\Model\Rules $ruleItem */
            foreach ($rulesCollection->getItems() as $ruleItem) {
                $resourceId = $ruleItem->getResourceId();
                if ($acl->has($resourceId) && $acl->isAllowed($role->getId(), $resourceId)) {
                    $allowedResources[] = $resourceId;
                }
            }
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new LocalizedException (
                'Error happened while getting a list of allowed resources. Check exception log for details.'
            );
        }
        return $allowedResources;
    }

    /**
     * {@inheritdoc}
     */
    public function removePermissions(UserIdentifier $userIdentifier)
    {
        try {
            $this->_deleteRole($userIdentifier);
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new LocalizedException (
                'Error happened while deleting role and permissions. Check exception log for details.'
            );
        }
    }

    /**
     * Create new ACL role.
     *
     * @param UserIdentifier $userIdentifier
     * @return Role
     * @throws NoSuchEntityException
     */
    protected function _createRole($userIdentifier)
    {
        $userType = $userIdentifier->getUserType();
        $userId = $userIdentifier->getUserId();
        switch ($userType) {
            case UserIdentifier::USER_TYPE_INTEGRATION:
                $roleName = $userType . $userId;
                $roleType = \Magento\User\Model\Acl\Role\User::ROLE_TYPE;
                $parentId = 0;
                $userId = $userIdentifier->getUserId();
                break;
            default:
                throw NoSuchEntityException::singleField('userType', $userType);
        }
        $role = $this->_roleFactory->create();
        $role->setRoleName(
            $roleName
        )->setUserType(
                $userType
            )->setUserId(
                $userId
            )->setRoleType(
                $roleType
            )->setParentId(
                $parentId
            )->save();
        return $role;
    }

    /**
     * Remove an ACL role. This deletes the cascading permissions
     *
     * @param UserIdentifier $userIdentifier
     * @return Role
     * @throws NoSuchEntityException
     */
    protected function _deleteRole($userIdentifier)
    {
        $userType = $userIdentifier->getUserType();
        $userId = $userIdentifier->getUserId();
        switch ($userType) {
            case UserIdentifier::USER_TYPE_INTEGRATION:
                $roleName = $userType . $userId;
                break;
            default:
                throw NoSuchEntityException::singleField('userType', $userType);
        }
        $role = $this->_roleFactory->create()->load($roleName, 'role_name');
        return $role->delete();
    }

    /**
     * Identify user role from user identifier.
     *
     * @param UserIdentifier $userIdentifier
     * @return Role|false Return false in case when no role associated with provided user was found.
     */
    protected function _getUserRole($userIdentifier)
    {
        $roleCollection = $this->_roleCollectionFactory->create();
        $userType = $userIdentifier->getUserType();
        $userId = $userIdentifier->getUserId();
        /** @var Role $role */
        $role = $roleCollection->setUserFilter($userId, $userType)->getFirstItem();
        return $role->getId() ? $role : false;
    }

    /**
     * Associate resources with the specified role. All resources previously assigned to the role will be unassigned.
     *
     * @param Role $role
     * @param string[] $resources
     * @return void
     */
    protected function _associateResourcesWithRole($role, array $resources)
    {
        /** @var \Magento\User\Model\Rules $rules */
        $rules = $this->_rulesFactory->create();
        $rules->setRoleId($role->getId())->setResources($resources)->saveRel();
    }
}
