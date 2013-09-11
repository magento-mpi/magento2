<?php
/**
 * Test for \Magento\Webapi\Model\Resource\Acl\Role.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::getRolesIds().
     *
     * @magentoDataFixture Magento/Webapi/_files/role.php
     * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesIds()
    {
        $expectedRoleNames = array('test_role', 'Test role');
        /** @var $roleResource \Magento\Webapi\Model\Resource\Acl\Role */
        $roleResource = Mage::getResourceModel('\Magento\Webapi\Model\Resource\Acl\Role');
        $rolesIds = $roleResource->getRolesIds();
        $this->assertCount(2, $rolesIds);
        foreach ($rolesIds as $roleId) {
            /** @var $role \Magento\Webapi\Model\Acl\Role */
            $role = Mage::getModel('\Magento\Webapi\Model\Acl\Role')->load($roleId);
            $this->assertNotEmpty($role->getId());
            $this->assertContains($role->getRoleName(), $expectedRoleNames);
        }
    }

    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::getRolesList().
     *
     * @magentoDataFixture Magento/Webapi/_files/role.php
     * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesList()
    {
        /** @var $roleResource \Magento\Webapi\Model\Resource\Acl\Role */
        $roleResource = Mage::getResourceModel('\Magento\Webapi\Model\Resource\Acl\Role');
        $rolesList = $roleResource->getRolesList();
        $this->assertCount(2, $rolesList);
        foreach ($rolesList as $roleId => $roleName) {
            $role = Mage::getModel('\Magento\Webapi\Model\Acl\Role')->load($roleId);
            $this->assertEquals($roleId, $role->getId());
            $this->assertEquals($roleName, $role->getRoleName());
        }
    }

    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::_initUniqueFields().
     *
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Role Name already exists.
     * @magentoDataFixture Magento/Webapi/_files/role.php
     */
    public function testInitUniqueFields()
    {
        /** @var $roleResource \Magento\Webapi\Model\Resource\Acl\Role */
        $roleResource = Mage::getResourceModel('\Magento\Webapi\Model\Resource\Acl\Role');
        $uniqueFields = $roleResource->getUniqueFields();
        $expectedUnique = array(
            array(
                'field' => 'role_name',
                'title' => 'Role Name'
            ),
        );
        $this->assertEquals($expectedUnique, $uniqueFields);

        Mage::getModel('\Magento\Webapi\Model\Acl\Role')
            ->setRoleName('test_role')
            ->save();
    }

    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::delete().
     *
     * @magentoDataFixture Magento/Webapi/_files/user_with_role.php
     */
    public function testDeleteRole()
    {
        Mage::getModel('\Magento\Webapi\Model\Acl\Role')
            ->load('Test role', 'role_name')
            ->delete();
        /** @var \Magento\Webapi\Model\Acl\User $user */
        $user = Mage::getModel('\Magento\Webapi\Model\Acl\User')
            ->load('test_username', 'api_key');
        $this->assertNotEmpty($user->getId());
    }
}
