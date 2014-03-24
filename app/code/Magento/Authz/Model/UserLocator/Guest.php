<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model\UserLocator;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authz\Model\UserLocatorInterface;

/**
 * Guest user locator.
 */
class Guest implements UserLocatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserType()
    {
        return UserIdentifier::USER_TYPE_GUEST;
    }
}
