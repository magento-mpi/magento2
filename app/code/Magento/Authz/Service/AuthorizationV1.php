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
use Magento\Authorization\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;
use Magento\Authorization\Model\Resource\Rules\CollectionFactory as RulesCollectionFactory;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\RoleFactory;
use Magento\Authorization\Model\RulesFactory;

/**
 * Authorization service.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthorizationV1 implements AuthorizationV1Interface
{
    const PERMISSION_ANONYMOUS = 'anonymous';
    const PERMISSION_SELF = 'self';

    /**
     * @var AclBuilder
     */
    protected $_aclBuilder;

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
     * @param RoleFactory $roleFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param RulesFactory $rulesFactory
     * @param RulesCollectionFactory $rulesCollectionFactory
     * @param Logger $logger
     * @param RootAclResource $rootAclResource
     */
    public function __construct(
        AclBuilder $aclBuilder,
        RoleFactory $roleFactory,
        RoleCollectionFactory $roleCollectionFactory,
        RulesFactory $rulesFactory,
        RulesCollectionFactory $rulesCollectionFactory,
        Logger $logger,
        RootAclResource $rootAclResource
    ) {
        $this->_aclBuilder = $aclBuilder;
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
    public function getAllowedResources(UserIdentifier $userIdentifier)
    {
        if ($userIdentifier->getUserType() == UserIdentifier::USER_TYPE_GUEST) {
            return [self::PERMISSION_ANONYMOUS];
        } elseif ($userIdentifier->getUserType() == UserIdentifier::USER_TYPE_CUSTOMER) {
            return [self::PERMISSION_SELF];
        }
        $allowedResources = [];
        try {
            $role = $this->_getUserRole($userIdentifier);
            if (!$role) {
                throw new AuthorizationException('The role associated with the specified user cannot be found.');
            }
            $rulesCollection = $this->_rulesCollectionFactory->create();
            $rulesCollection->getByRoles($role->getId())->load();
            $acl = $this->_aclBuilder->getAcl();
            /** @var \Magento\Authorization\Model\Rules $ruleItem */
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
            throw new LocalizedException(
                'Error happened while getting a list of allowed resources. Check exception log for details.'
            );
        }
        return $allowedResources;
    }

    /**
     * Identify user role from user identifier.
     *
     * @param UserIdentifier $userIdentifier
     * @return \Magento\Authorization\Model\Role|false Return false in case when no role associated with provided user was found.
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
