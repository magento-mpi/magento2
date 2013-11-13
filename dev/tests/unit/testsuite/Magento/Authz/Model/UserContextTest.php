<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Model;

use \Magento\Authz\Model\UserContext;

/**
 * Tests for User Context.
 */
class UserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $userType
     * @param int $userId
     * @dataProvider constructProvider
     */
    public function testConstruct($userType, $userId)
    {
        $context = new UserContext($userType, $userId);
        $this->assertEquals($userId, $context->getUserId());
        $this->assertEquals($userType, $context->getUserType());
    }

    /**
     * @param string $userType
     * @param int $userId
     * @param string $exceptionMessage
     * @dataProvider constructProviderInvalidData
     */
    public function testConstructInvalidData($userType, $userId, $exceptionMessage)
    {
        $this->setExpectedException('\LogicException', $exceptionMessage);
        new UserContext($userType, $userId);
    }

    public function constructProvider()
    {
        return array(
            array(UserContext::USER_TYPE_GUEST, 0),
            array(UserContext::USER_TYPE_CUSTOMER, 1),
            array(UserContext::USER_TYPE_ADMIN, 2),
            array(UserContext::USER_TYPE_INTEGRATION, 3),
        );
    }

    public function constructProviderInvalidData()
    {
        return array(
            array('InvalidUserType', 1,
                'Invalid user type: \'InvalidUserType\'. Allowed types: Guest, Customer, Admin, Integration'),
            array(UserContext::USER_TYPE_CUSTOMER, -1, 'Invalid user ID: \'-1\''),
            array(UserContext::USER_TYPE_ADMIN, 'InvalidUserId', 'Invalid user ID: \'InvalidUserId\''),
            array(UserContext::USER_TYPE_GUEST, 3, 'Guest user must not have user ID set.'),
        );
    }
}
