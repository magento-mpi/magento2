<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Role resource test
 *
 * @magentoAppArea adminhtml
 */
class Mage_User_Model_Resource_RoleTest extends PHPUnit_Framework_TestCase
{
    public function testGetRoleUsers()
    {
        $role = Mage::getModel('Mage_User_Model_Role');
        $roleResource = Mage::getResourceModel('Mage_User_Model_Resource_Role');

        $this->assertEmpty($roleResource->getRoleUsers($role));

        $role->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($roleResource->getRoleUsers($role));
    }
}
