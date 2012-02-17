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
 * API2 User Customer Mock Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User_Customer_Mock extends Mage_Api2_Model_Auth_User_Customer
{
    /**
     * User Role rewrite for test purposes
     *
     * @var string
     */
    public $_role;

    /**
     * Set user role
     *
     * @param int $role
     * @return Mage_Api2_Model_Auth_User_Customer_Mock
     */
    public function setRole($role)
    {
        $this->_role = $role;

        return $this;
    }
}

/**
 * Test Api2 User Customer model
 */
class Mage_Api2_Model_Auth_User_CustomerTest extends Mage_PHPUnit_TestCase
{
    /**
     * API2 User Customer model
     *
     * @var Mage_Api2_Model_Auth_User_Customer
     */
    protected $_customer;

    /**
     * API2 Role model mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_customer = Mage::getModel('api2/auth_user_customer');
        $this->_roleMock = $this->getModelMockBuilder('api2/acl_global_role')
            ->setMethods(array('load', 'getId'))
            ->getMock();
    }

    /**
     * Test guest role ID
     */
    public function testGuestRoleId()
    {
        $this->assertInternalType('integer', Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID);
    }

    /**
     * Test getRole method
     */
    public function testGetRole()
    {
        $this->_roleMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->_roleMock));

        $this->_roleMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID));

        $this->assertEquals(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID, $this->_customer->getRole());
    }

    /**
     * Test getRole method second call
     */
    public function testGetRoleSecondCall()
    {
        $this->_roleMock->expects($this->never())
            ->method('load');

        $this->_roleMock->expects($this->never())
            ->method('getId');

        $customerMock = new Mage_Api2_Model_Auth_User_Customer_Mock;
        $customerMock->setRole(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID);

        $this->assertEquals(Mage_Api2_Model_Acl_Global_Role::ROLE_CUSTOMER_ID, $customerMock->getRole());
    }

    /**
     * Test getRole method
     */
    public function testGetRoleNotFound()
    {
        $this->_roleMock->expects($this->once())
            ->method('load')
            ->will($this->returnValue($this->_roleMock));

        $this->_roleMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        try {
            $this->_customer->getRole();
        } catch (Exception $e) {
            $this->assertEquals('Customer role not found', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test getType method
     */
    public function testGetType()
    {
        $this->assertEquals('customer', Mage::getModel('api2/auth_user_customer')->getType());
    }
}
