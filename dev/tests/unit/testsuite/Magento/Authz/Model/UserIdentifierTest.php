<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Model;

use Magento\Authz\Model\UserIdentifier;

/**
 * Tests for User identifier.
 */
class UserIdentifierTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_userContextMock;

    protected function setUp()
    {
        parent::setUp();
        $this->_userContextMock = $this->getMock(
            'Magento\Authorization\Model\UserContextInterface',
            array('getUserId', 'getUserType')
        );
    }

    /**
     * @param string $userType
     * @param int $userId
     * @dataProvider constructProvider
     */
    public function testConstruct($userType, $userId)
    {
        $context = new UserIdentifier($this->_userContextMock, $userType, $userId);
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
        new UserIdentifier($this->_userContextMock, $userType, $userId);
    }

    public function constructProvider()
    {
        return array(
            array(UserIdentifier::USER_TYPE_GUEST, 0),
            array(UserIdentifier::USER_TYPE_CUSTOMER, 1),
            array(UserIdentifier::USER_TYPE_ADMIN, 2),
            array(UserIdentifier::USER_TYPE_INTEGRATION, 3)
        );
    }

    public function constructProviderInvalidData()
    {
        return array(
            array(
                'InvalidUserType',
                1,
                'Invalid user type: \'InvalidUserType\'. Allowed types: Guest, Customer, Admin, Integration'
            ),
            array(UserIdentifier::USER_TYPE_CUSTOMER, -1, 'Invalid user ID: \'-1\''),
            array(UserIdentifier::USER_TYPE_ADMIN, 'InvalidUserId', 'Invalid user ID: \'InvalidUserId\''),
            array(UserIdentifier::USER_TYPE_GUEST, 3, 'Guest user must not have user ID set.')
        );
    }
}
