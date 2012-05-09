<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Admin
 */
class Mage_User_Model_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_User
     */
    protected $_model;

    /**
     * @var Mage_User_Model_Role
     */
    protected static $_newRole;

    protected function setUp()
    {
        $this->_model = new Mage_User_Model_User;
    }

    /**
     * Ensure that an exception is not thrown, if the user does not exist
     */
    public function testLoadByUsername()
    {
        $this->_model->loadByUsername('non_existing_user');
        $this->assertNull($this->_model->getId(), 'The admin user has an unexpected ID');
        $this->_model->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        $this->assertNotEmpty($this->_model->getId(), 'The admin user should have been loaded');
    }

    /**
     * Test that user role is updated after save
     *
     * @magentoDataFixture roleDataFixture
     */
    public function testUpdateRoleOnSave()
    {
        $this->_model->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        $this->assertEquals('Administrators', $this->_model->getRole()->getRoleName());
        $this->_model->setRoleId(self::$_newRole->getId())->save();
        $this->assertEquals('admin_role', $this->_model->getRole()->getRoleName());
    }

    public static function roleDataFixture()
    {
        self::$_newRole = new Mage_User_Model_Role;
        self::$_newRole->setName('admin_role')
            ->setRoleType('G')
            ->setPid('1');
        self::$_newRole->save();
    }

    public function testGetRole()
    {
        $this->_model->loadByUsername(Magento_Test_Bootstrap::ADMIN_NAME);
        $this->assertInstanceOf('Mage_User_Model_Role', $this->_model->getRole());
    }

}
