<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Api2 Auth model
 */
class Mage_Api2_Model_AuthTest extends Mage_PHPUnit_TestCase
{
    /**
     * Authentication manager object
     *
     * @var Mage_Api2_Model_Auth
     */
    protected $_authManager;

    /**
     * Mock for API2 helper object
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * Request object
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_request     = Mage::getSingleton('api2/request');
        $this->_authManager = Mage::getModel('api2/auth');
        $this->_helperMock  = $this->getHelperMockBuilder('api2/data')->setMethods(array('getUserTypes'))->getMock();
    }

    /**
     * Test authenticate method with no user types
     */
    public function testAuthenticateNoAllowedTypes()
    {
        $this->_helperMock->expects($this->once())
            ->method('getUserTypes')
            ->will($this->returnValue(array()));

        try {
            $this->_authManager->authenticate($this->_request);
        } catch (Exception $e) {
            $this->assertEquals(
                'No allowed user types found', $e->getMessage(), 'Expected exception message does not match'
            );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test authenticate method with invalid/not allowed
     */
    public function testAuthenticateInvalidType()
    {
        $this->_helperMock->expects($this->once())
            ->method('getUserTypes')
            ->will($this->returnValue(array('admin' => 'api2/auth_user_admin')));

        $this->getModelMockBuilder('api2/auth_adapter')
            ->setMethods(array('getUserParams'))
            ->getMock()
            ->expects($this->once())
            ->method('getUserParams')
            ->will($this->returnValue((object) array('type' => 'guest', 'id' => null)));

        $this->setExpectedException('Mage_Api2_Exception', 'Invalid user type or type is not allowed');

        $this->_authManager->authenticate($this->_request);
    }

    /**
     * Test authenticate method with invalid user model
     */
    public function testAuthenticateInvalidUserModel()
    {
        $userType = 'admin';

        $this->_helperMock->expects($this->once())
            ->method('getUserTypes')
            ->will($this->returnValue(array($userType => 'catalog/product')));

        $this->getModelMockBuilder('api2/auth_adapter')
            ->setMethods(array('getUserParams'))
            ->getMock()
            ->expects($this->once())
            ->method('getUserParams')
            ->will($this->returnValue((object) array('type' => $userType, 'id' => 5)));

        try {
            $this->_authManager->authenticate($this->_request);
        } catch (Exception $e) {
            $this->assertEquals(
                'User model must to extend Mage_Api2_Model_Auth_User_Abstract',
                $e->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test authenticate method with full valid data
     */
    public function testAuthenticate()
    {
        $userType = 'admin';

        $this->_helperMock->expects($this->once())
            ->method('getUserTypes')
            ->will($this->returnValue(array($userType => 'api2/auth_user_admin')));

        $this->getModelMockBuilder('api2/auth_adapter')
            ->setMethods(array('getUserParams'))
            ->getMock()
            ->expects($this->once())
            ->method('getUserParams')
            ->will($this->returnValue((object) array('type' => $userType, 'id' => 5)));

        $this->assertInstanceOf(
            'Mage_Api2_Model_Auth_User_Abstract', $this->_authManager->authenticate($this->_request)
        );
    }

    /**
     * Test authenticate method with inconsistent user type
     */
    public function testAuthenticateTypeInconsistent()
    {
        $userType = 'admin';

        $this->_helperMock->expects($this->once())
            ->method('getUserTypes')
            ->will($this->returnValue(array($userType => 'api2/auth_user_admin')));

        $this->getModelMockBuilder('api2/auth_adapter')
            ->setMethods(array('getUserParams'))
            ->getMock()
            ->expects($this->once())
            ->method('getUserParams')
            ->will($this->returnValue((object) array('type' => $userType, 'id' => 5)));

        $userMock = $this->getModelMockBuilder('api2/auth_user_admin')->setMethods(array('getType'))->getMock();

        $userMock->expects($this->once())
            ->method('getType')
            ->will($this->returnValue('guest'));

        try {
            $this->_authManager->authenticate($this->_request);
        } catch (Exception $e) {
            $this->assertEquals(
                'User model type does not match appropriate type in config',
                $e->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
