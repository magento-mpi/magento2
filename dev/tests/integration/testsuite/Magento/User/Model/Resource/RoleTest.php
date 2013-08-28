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
 * Role resource test
 *
 * @magentoAppArea adminhtml
 */
class Magento_User_Model_Resource_RoleTest extends PHPUnit_Framework_TestCase
{
    public function testGetRoleUsers()
    {
        $role = Mage::getModel('Magento_User_Model_Role');
        $roleResource = Mage::getResourceModel('Magento_User_Model_Resource_Role');

        $this->assertEmpty($roleResource->getRoleUsers($role));

        $role->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($roleResource->getRoleUsers($role));
    }
}
