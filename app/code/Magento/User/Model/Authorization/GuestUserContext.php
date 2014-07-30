<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model\Authorization;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Guest user context
 */
class GuestUserContext implements UserContextInterface
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
