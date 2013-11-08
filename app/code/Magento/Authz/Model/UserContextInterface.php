<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Model;

/**
 * User context interface. By user can be understood admin, customer, guest, web API integration.
 */
interface UserContextInterface
{
    /**#@+
     * Authorization user types
     */
    const USER_TYPE_GUEST = 'Guest';
    const USER_TYPE_CUSTOMER = 'Customer';
    const USER_TYPE_ADMIN = 'Admin';
    const USER_TYPE_INTEGRATION = 'Integration';
    /**#@-*/

    /**
     * Set user type (admin, customer, guest, web API integration)
     *
     * @param string $userType
     */
    public function setUserType($userType);

    /**
     * Retrieve user type (admin, customer, guest, web API integration)
     *
     * @return string
     */
    public function getUserType();

    /**
     * Set user ID.
     *
     * @param int
     */
    public function setUserId($userId);

    /**
     * Get user ID. Null is possible when user type is 'guest'.
     *
     * @return int|null
     */
    public function getUserId();
}
