<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Service;

use Magento\Authz\Model\UserContext;

/**
 * Authorization service interface.
 */
interface AuthorizationV1Interface
{
    /**
     * Grant permissions to user to access the specified resources.
     *
     * @param UserContext $userContext
     * @param string[] $resources List of resources which should be available to the specified user.
     */
    public function grantPermissions($userContext, $resources);

    /**
     * Check if the user has permission to access the requested resources.
     *
     * Access is prohibited if there is a lack of permissions to any of the requested resources.
     *
     * @param string|string[] $resources Single resource or a list of resources
     * @param UserContext|null $userContext Context of current user is used by default
     * @return bool
     */
    public function isAllowed($resources, $userContext = null);
}
