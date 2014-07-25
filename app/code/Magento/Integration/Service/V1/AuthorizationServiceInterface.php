<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service\V1;

use Magento\Authz\Model\UserIdentifier;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface for integration permissions management.
 */
interface AuthorizationServiceInterface
{
    /**
     * Grant permissions to user to access the specified resources.
     *
     * @param UserIdentifier $userIdentifier
     * @param string[] $resources List of resources which should be available to the specified user.
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function grantPermissions(UserIdentifier $userIdentifier, $resources);

    /**
     * Grant permissions to the user to access all resources available in the system.
     *
     * @param UserIdentifier $userIdentifier
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function grantAllPermissions(UserIdentifier $userIdentifier);

    /**
     * Remove user role and associated permissions.
     *
     * @param UserIdentifier $userIdentifier
     * @return void
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function removePermissions(UserIdentifier $userIdentifier);
}
