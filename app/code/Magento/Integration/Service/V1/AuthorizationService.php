<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Authorization\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;
use Magento\Authorization\Model\Resource\Rules\CollectionFactory as RulesCollectionFactory;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\RoleFactory;
use Magento\Authorization\Model\RulesFactory;
use Magento\Authz\Model\UserIdentifier;
use Magento\Framework\Acl;
use Magento\Framework\Acl\Builder as AclBuilder;
use Magento\Framework\Acl\RootResource as RootAclResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Logger;

/**
 * Service for integration permissions management.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizationService implements AuthorizationServiceInterface
{
    const PERMISSION_ANONYMOUS = 'anonymous';
    const PERMISSION_SELF = 'self';

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
    public function grantPermissions(UserIdentifier $userIdentifier, $resources)
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
    public function removePermissions(UserIdentifier $userIdentifier)
    {
        try {
            $this->_deleteRole($userIdentifier);
        } catch (NoSuchEntityException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new LocalizedException(
                'Error happened while deleting role and permissions. Check exception log for details.'
            );
        }
    }

    /**
     * Create new ACL role.
     *
     * @param UserIdentifier $userIdentifier
     * @return \Magento\Authorization\Model\Role
     * @throws NoSuchEntityException
     * @throws \LogicException
     */
    protected function _createRole($userIdentifier)
    {
        $userType = $userIdentifier->getUserType();
        if (!$this->_canRoleBeCreatedForUserType($userType)) {
            throw new \LogicException("The role with user type '{$userType}' cannot be created");
        }
        $userId = $userIdentifier->getUserId();
        switch ($userType) {
            case UserIdentifier::USER_TYPE_INTEGRATION:
                $roleName = $userType . $userId;
                $roleType = \Magento\Authorization\Model\Acl\Role\User::ROLE_TYPE;
                $parentId = 0;
                $userId = $userIdentifier->getUserId();
                break;
            default:
                throw NoSuchEntityException::singleField('userType', $userType);
        }
        $role = $this->_roleFactory->create();
        $role->setRoleName($roleName)
            ->setUserType($userType)
            ->setUserId($userId)
            ->setRoleType($roleType)
            ->setParentId($parentId)
            ->save();
        return $role;
    }

    /**
     * Remove an ACL role. This deletes the cascading permissions
     *
     * @param UserIdentifier $userIdentifier
     * @return \Magento\Authorization\Model\Role
     * @throws NoSuchEntityException
     * @throws \LogicException
     */
    protected function _deleteRole($userIdentifier)
    {
        $userType = $userIdentifier->getUserType();
        if (!$this->_canRoleBeCreatedForUserType($userType)) {
            throw new \LogicException("The role with user type '{$userType}' cannot be created or deleted.");
        }
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
     * @return \Magento\Authorization\Model\Role|false Return false in case when no role associated with user was found.
     * @throws \LogicException
     */
    protected function _getUserRole($userIdentifier)
    {
        if (!$this->_canRoleBeCreatedForUserType($userIdentifier->getUserType())) {
            throw new \LogicException(
                "The role with user type '{$userIdentifier->getUserType()}' does not exist and cannot be created"
            );
        }
        $roleCollection = $this->_roleCollectionFactory->create();
        $userType = $userIdentifier->getUserType();
        /** @var Role $role */
        $userId = $userIdentifier->getUserId();
        $role = $roleCollection->setUserFilter($userId, $userType)->getFirstItem();
        return $role->getId() ? $role : false;
    }

    /**
     * Associate resources with the specified role. All resources previously assigned to the role will be unassigned.
     *
     * @param \Magento\Authorization\Model\Role $role
     * @param string[] $resources
     * @return void
     * @throws \LogicException
     */
    protected function _associateResourcesWithRole($role, array $resources)
    {
        /** @var \Magento\Authorization\Model\Rules $rules */
        $rules = $this->_rulesFactory->create();
        $rules->setRoleId($role->getId())->setResources($resources)->saveRel();
    }

    /**
     * Check if there role can be associated with user having provided user type.
     *
     * Roles cannot be created for guests and customers.
     *
     * @param int $userType
     * @return bool
     */
    protected function _canRoleBeCreatedForUserType($userType)
    {
        return ($userType != UserIdentifier::USER_TYPE_CUSTOMER) && ($userType != UserIdentifier::USER_TYPE_GUEST);
    }
}
