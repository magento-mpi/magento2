<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserIdentifier;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Authorization service interface.
 */
interface AuthorizationV1Interface
{
    /**
     * Get a list of resources available to the specified user.
     *
     * @param UserIdentifier $userIdentifier
     * @return string[]
     * @throws AuthorizationException
     * @throws LocalizedException
     */
    public function getAllowedResources(UserIdentifier $userIdentifier);
}
