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
    public function grantPermissions($userIdentifier, $resources);

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
    public function isAllowed($resources, $userIdentifier = null);
}
