<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Service;

use Magento\Authz\Model\UserIdentifier;
use Magento\Service\Exception as ServiceException;

/**
 * Authorization service interface.
 */
interface AuthorizationV1Interface
{
    /**
     * Grant permissions to user to access the specified resources.
     *
     * @param UserIdentifier $userIdentifier
     * @param string[] $resources List of resources which should be available to the specified user.
     * @throws ServiceException
     */
    public function grantPermissions(UserIdentifier $userIdentifier, array $resources);

    /**
     * Grant permissions to the user to access all resources available in the system.
     *
     * @param UserIdentifier $userIdentifier
     * @throws ServiceException
     */
    public function grantAllPermissions(UserIdentifier $userIdentifier);

    /**
     * Check if the user has permission to access the requested resources.
     *
     * Access is prohibited if there is a lack of permissions to any of the requested resources.
     *
     * @param string|string[] $resources Single resource or a list of resources
     * @param UserIdentifier|null $userIdentifier Context of current user is used by default
     * @return bool
     * @throws ServiceException
     */
    public function isAllowed($resources, UserIdentifier $userIdentifier = null);

    /**
     * Get a list of resources available to the specified user.
     *
     * @param UserIdentifier $userIdentifier
     * @return string[]
     * @throws ServiceException
     */
    public function getAllowedResources(UserIdentifier $userIdentifier);

    /**
     * Remove user role and associated permissions.
     *
     * @param UserIdentifier $userIdentifier
     * @throws ServiceException
     */
    public function removePermissions(UserIdentifier $userIdentifier);
}
