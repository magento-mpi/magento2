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
 * API2 User Admin Mock Class
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User_Admin_Mock extends Mage_Api2_Model_Auth_User_Admin
{
    /**
     * User Role rewrite for test purposes
     *
     * @var string
     */
    public $_role;
}

/**
 * API2 global ACL role resource collection mock class
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_Role_Collection_Mock
{
    /**
     * Add filter by admin user id and join table with appropriate information
     *
     * @return Mage_Api2_Model_Resource_Acl_Global_Role_Collection_Mock
     */
    public function addFilterByAdminId()
    {
        return $this;
    }

    /**
     * Retrieve collection first item
     *
     * @return Mage_Api2_Model_Resource_Acl_Global_Role_Collection_Mock
     */
    public function getFirstItem()
    {
        return $this;
    }

    /**
     * Retrieve collection item id
     *
     * @return Mage_Api2_Model_Resource_Acl_Global_Role_Collection_Mock
     */
    public function getId()
    {
        return null;
    }
}

/**
 * Test Api2 User Admin model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Auth_User_AdminTest extends Mage_PHPUnit_TestCase
{
    /**
     * API User object
     *
     * @var Mage_Api2_Model_Auth_User_Admin_Mock
     */
    protected $_userMock;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_userMock = new Mage_Api2_Model_Auth_User_Admin_Mock;
    }

    /**
     * Test getRole method
     */
    public function testGetRole()
    {
        $this->_userMock->_role = 'admin';

        $this->assertEquals('admin', $this->_userMock->getRole());
    }

    /**
     * Test getRole method
     */
    public function testGetRoleUserIdNotSet()
    {
        try {
            $this->_userMock->getRole();
        } catch (Exception $e) {
            $this->assertEquals('Admin identifier is not set', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test getRole method
     */
    public function testGetRoleNotSet()
    {
        /** @var $roleMock PHPUnit_Framework_MockObject_MockObject */
        $roleMock = $this->getModelMockBuilder('api2/acl_global_role')
            ->setMethods(array('getCollection'))
            ->getMock();

        $roleMock->expects($this->once())
            ->method('getCollection')
            ->will($this->returnValue(new Mage_Api2_Model_Resource_Acl_Global_Role_Collection_Mock()));

        $this->_userMock->setUserId(1);

        try {
            $this->_userMock->getRole();
        } catch (Exception $e) {
            $this->assertEquals('Admin role not found', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test setRole method
     */
    public function testSetRole()
    {
        $this->_userMock->setRole('admin');

        $this->assertEquals('admin', $this->_userMock->_role);
    }

    /**
     * Test setRole method
     */
    public function testSetRoleMoreThanOnce()
    {
        $this->_userMock->setRole('admin');

        try {
            $this->_userMock->setRole('admin');
        } catch (Exception $e) {
            $this->assertEquals('Admin role has been already set', $e->getMessage(), 'Invalid exception message');

            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    /**
     * Test getType method
     */
    public function testGetType()
    {
        $this->assertEquals('admin', $this->_userMock->getType());
    }
}
