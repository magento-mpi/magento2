<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Model;

/**
 * User context class. By user can be understood admin, customer, guest, web API integration.
 */
class UserContext
{
    /**#@+
     * Authorization user types.
     */
    const USER_TYPE_GUEST = 'Guest';
    const USER_TYPE_CUSTOMER = 'Customer';
    const USER_TYPE_ADMIN = 'Admin';
    const USER_TYPE_INTEGRATION = 'Integration';
    /**#@-*/

    /**
     * User type (admin, customer, guest, web API integration).
     *
     * @var string
     */
    protected $_userType;

    /** @var  int */
    protected $_userId;

    /**
     * Initialize user type and user id
     *
     * @param string $userType
     * @param int $userId
     */
    public function __construct($userType, $userId = 0)
    {
        $this->_setUserId($userId);
        $this->_setUserType($userType);
    }

    /**
     * Get user ID. Null is possible when user type is 'guest'.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->_userId;
    }

    /**
     * Retrieve user type (admin, customer, guest, web API integration).
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->_userType;
    }

    /**
     * Set user ID.
     *
     * @param int
     * @return UserContext
     * @throws \LogicException
     */
    protected function _setUserId($userId)
    {
        if (!is_integer($userId) || ($userId <= 0)) {
            throw new \LogicException("Invalid user ID: '{$userId}'.");
        }
        $this->_userId = $userId;
        return $this;
    }

    /**
     * Set user type.
     *
     * @param string $userType
     * @return UserContext
     * @throws \LogicException
     */
    protected function _setUserType($userType)
    {
        $availableTypes = array(
            self::USER_TYPE_GUEST,
            self::USER_TYPE_CUSTOMER,
            self::USER_TYPE_ADMIN,
            self::USER_TYPE_INTEGRATION
        );
        if (!in_array($userType, $availableTypes)) {
            throw new \LogicException(
                "Invalid user type: '{$userType}'. Allowed types: " . implode(", ", $availableTypes)
            );
        }
        $this->_userId = $userType;
        return $this;
    }
}
