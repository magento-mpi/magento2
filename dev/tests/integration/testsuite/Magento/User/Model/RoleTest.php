<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Model_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Model_Role
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_User_Model_Role');
    }

    public function testGetUsersCollection()
    {
        $this->assertInstanceOf('Magento_User_Model_Resource_Role_User_Collection',
            $this->_model->getUsersCollection());
    }

    public function testGetRoleUsers()
    {
        $this->assertEmpty($this->_model->getRoleUsers());

        $this->_model->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($this->_model->getRoleUsers());
    }
}
