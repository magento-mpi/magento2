<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model;

/**
 * User identifier class. By user can be understood admin, customer, guest, web API integration.
 */
class UserIdentifier
{
    /**#@+
     * User types.
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

    /**
     * @var  int
     */
    protected $_userId;

    /**
     * Initialize user type and user id.
     *
     * @param UserLocatorInterface $userLocator Locator of active user.
     * @param string|null $userType
     * @param int|null $userId
     * @throws \LogicException
     */
    public function __construct(UserLocatorInterface $userLocator, $userType = null, $userId = null)
    {
        $userType = isset($userType) ? $userType : $userLocator->getUserType();
        $userId = isset($userId) ? $userId : $userLocator->getUserId();
        if ($userType == self::USER_TYPE_GUEST && $userId) {
            throw new \LogicException('Guest user must not have user ID set.');
        }
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
     * @param int $userId
     * @return $this
     * @throws \LogicException
     */
    protected function _setUserId($userId)
    {
        $userId = is_numeric($userId) ? (int)$userId : $userId;
        if (!is_integer($userId) || ($userId < 0)) {
            throw new \LogicException("Invalid user ID: '{$userId}'.");
        }
        $this->_userId = $userId;
        return $this;
    }

    /**
     * Set user type.
     *
     * @param string $userType
     * @return $this
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
        $this->_userType = $userType;
        return $this;
    }
}
