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

namespace Magento\User\Model\Resource;

/**
 * Role resource test
 *
 * @magentoAppArea adminhtml
 */
class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRoleUsers()
    {
        $role = \Mage::getModel('Magento\User\Model\Role');
        $roleResource = \Mage::getResourceModel('Magento\User\Model\Resource\Role');

        $this->assertEmpty($roleResource->getRoleUsers($role));

        $role->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($roleResource->getRoleUsers($role));
    }
}
