<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Framework\Exception\LocalizedException;

/**
 * Interface for integration permissions management.
 */
interface AuthorizationServiceInterface
{
    /**#@+
     * Permission types for guest/anonymous and self access
     */
    const PERMISSION_ANONYMOUS = 'anonymous';
    const PERMISSION_SELF = 'self';
    /**#@- */

    /**
     * Grant permissions to user to access the specified resources.
     *
     * @param int $integrationId
     * @param string[] $resources List of resources which should be available to the specified user.
     * @return void
     * @throws LocalizedException
     */
    public function grantPermissions($integrationId, $resources);

    /**
     * Grant permissions to the user to access all resources available in the system.
     *
     * @param int $integrationId
     * @return void
     * @throws LocalizedException
     */
    public function grantAllPermissions($integrationId);

    /**
     * Remove role and associated permissions for the specified integration.
     *
     * @param int $integrationId
     * @return void
     * @throws LocalizedException
     */
    public function removePermissions($integrationId);
}
