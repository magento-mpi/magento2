<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Model;

/**
 * Interface for current user identification.
 */
interface UserContextInterface
{
    /**#@+
     * User types
     */
    const USER_TYPE_INTEGRATION = 1;
    const USER_TYPE_ADMIN = 2;
    const USER_TYPE_CUSTOMER = 3;
    const USER_TYPE_GUEST = 4;
    /**#@-*/

    /**
     * Identify current user ID.
     *
     * @return int|null
     */
    public function getUserId();

    /**
     * Retrieve current user type.
     *
     * @return string
     */
    public function getUserType();
}
