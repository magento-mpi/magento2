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
 * Test Api2 User abstract model (Admin, Customer, Guest etc.)
 */
class Mage_Api2_Model_Auth_Adapter_UserTest extends Mage_PHPUnit_TestCase
{
    /**
     * API User object
     *
     * @var Mage_Api2_Model_Auth_User_Admin_Stub
     */
    protected $_userStub;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_userStub = Mage::getModel('api2/auth_user_admin_stub');

        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_userStub = null;
    }

    /**
     * Test getRole method
     */
    public function testGetRole()
    {
        $this->_userStub->_role = 'admin';

        $this->assertEquals('admin', $this->_userStub->getRole());
    }

    /**
     * Test getRole method
     */
    public function testGetRoleNotSet()
    {
        try {
            $this->_userStub->getRole();
        } catch (Exception $e) {
            $this->assertEquals('User role is not set', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test setRole method
     */
    public function testSetRole()
    {
        $this->_userStub->setRole('admin');

        $this->assertEquals('admin', $this->_userStub->_role);
    }

    /**
     * Test setRole method
     */
    public function testSetRoleMoreThanOnce()
    {
        $this->_userStub->setRole('admin');

        try {
            $this->_userStub->setRole('admin');
        } catch (Exception $e) {
            $this->assertEquals('User role has been already set', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }
}
