<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
        $this->_helperMock  = $this->getHelperMockBuilder('api2/data')->setMethods(array('getUserRoles'))->getMock();
    }

    /**
     * Test authenticate method with no roles
     */
    public function testAuthenticateNoAllowedRoles()
    {
        $this->_helperMock->expects($this->once())
            ->method('getUserRoles')
            ->will($this->returnValue(array()));

        try {
            $this->_authManager->authenticate($this->_request);
        } catch (Exception $e) {
            $this->assertEquals(
                'No allowed user roles found', $e->getMessage(), 'Expected exception message does not match'
            );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test authenticate method with invalid/not allowed
     */
    public function testAuthenticateInvalidRole()
    {
        $this->_helperMock->expects($this->once())
            ->method('getUserRoles')
            ->will($this->returnValue(array('admin' => 'api2/auth_user_admin')));

        $authAdapterMock = $this->getModelMockBuilder('api2/auth_adapter')->setMethods(array('getUserRole'))->getMock();

        $authAdapterMock->expects($this->once())
            ->method('getUserRole')
            ->will($this->returnValue('guest'));

        try {
            $this->_authManager->authenticate($this->_request);
        } catch (Exception $e) {
            $this->assertEquals(
                'Invalid user role or role is not allowed',
                $e->getMessage(),
                'Expected exception message does not match'
            );
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test authenticate method with invalid user model
     */
    public function testAuthenticateInvalidUserModel()
    {
        $userRole = 'admin';

        $this->_helperMock->expects($this->once())
            ->method('getUserRoles')
            ->will($this->returnValue(array($userRole => 'catalog/product')));

        $authAdapterMock = $this->getModelMockBuilder('api2/auth_adapter')->setMethods(array('getUserRole'))->getMock();

        $authAdapterMock->expects($this->once())
            ->method('getUserRole')
            ->will($this->returnValue($userRole));

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
        $userRole = 'admin';

        $this->_helperMock->expects($this->once())
            ->method('getUserRoles')
            ->will($this->returnValue(array($userRole => 'api2/auth_user_admin')));

        $authAdapterMock = $this->getModelMockBuilder('api2/auth_adapter')->setMethods(array('getUserRole'))->getMock();

        $authAdapterMock->expects($this->once())
            ->method('getUserRole')
            ->will($this->returnValue($userRole));

        $userMock = $this->getModelMockBuilder('api2/auth_user_admin')->setMethods(array('setRole'))->getMock();

        $userMock->expects($this->once())
            ->method('setRole')
            ->with($userRole);

        $this->assertInstanceOf(
            'Mage_Api2_Model_Auth_User_Abstract', $this->_authManager->authenticate($this->_request)
        );
    }
}
