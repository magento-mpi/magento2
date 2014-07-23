<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Authorization\RoleLocator;
use Magento\Authorization\Model\Role;
use Magento\Authorization\Model\Resource\Role\CollectionFactory as RoleCollectionFactory;

class WebapiRoleLocator implements RoleLocator
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @var RoleCollectionFactory
     */
    protected $roleCollectionFactory;

    /**
     * Constructs a role locator using the user context.
     *
     * @param UserContextInterface $userContext
     * @param RoleCollectionFactory $roleCollectionFactory
     */
    public function __construct(
        UserContextInterface $userContext,
        RoleCollectionFactory $roleCollectionFactory
    ) {
        $this->userContext = $userContext;
        $this->roleCollectionFactory = $roleCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAclRoleId()
    {
        $userId = $this->userContext->getUserId();
        $userType = $this->userContext->getUserType();

        $roleCollection = $this->roleCollectionFactory->create();
        /** @var Role $role */
        $role = $roleCollection->setUserFilter($userId, $userType)->getFirstItem();

        if (!$role->getId()) {
            return null;
        }

        return $role->getId();
    }
}
