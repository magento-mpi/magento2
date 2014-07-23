<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model\UserLocator;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Guest user locator.
 */
class Guest implements UserContextInterface
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
