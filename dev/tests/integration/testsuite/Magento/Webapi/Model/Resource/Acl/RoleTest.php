<?php
/**
 * Test for Magento_Webapi_Model_Resource_Acl_Role.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Resource_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::getRolesIds().
     *
     * @magentoDataFixture Magento/Webapi/_files/role.php
     * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesIds()
    {
        $expectedRoleNames = array('test_role', 'Test role');
        /** @var $roleResource Magento_Webapi_Model_Resource_Acl_Role */
        $roleResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Resource_Acl_Role');
        $rolesIds = $roleResource->getRolesIds();
        $this->assertCount(2, $rolesIds);
        foreach ($rolesIds as $roleId) {
            /** @var $role Magento_Webapi_Model_Acl_Role */
            $role = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_Role')->load($roleId);
            $this->assertNotEmpty($role->getId());
            $this->assertContains($role->getRoleName(), $expectedRoleNames);
        }
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::getRolesList().
     *
     * @magentoDataFixture Magento/Webapi/_files/role.php
     * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
     */
    public function testGetRolesList()
    {
        /** @var $roleResource Magento_Webapi_Model_Resource_Acl_Role */
        $roleResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Resource_Acl_Role');
        $rolesList = $roleResource->getRolesList();
        $this->assertCount(2, $rolesList);
        foreach ($rolesList as $roleId => $roleName) {
            $role = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_Role')->load($roleId);
            $this->assertEquals($roleId, $role->getId());
            $this->assertEquals($roleName, $role->getRoleName());
        }
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::_initUniqueFields().
     *
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Role Name already exists.
     * @magentoDataFixture Magento/Webapi/_files/role.php
     */
    public function testInitUniqueFields()
    {
        /** @var $roleResource Magento_Webapi_Model_Resource_Acl_Role */
        $roleResource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Resource_Acl_Role');
        $uniqueFields = $roleResource->getUniqueFields();
        $expectedUnique = array(
            array(
                'field' => 'role_name',
                'title' => 'Role Name'
            ),
        );
        $this->assertEquals($expectedUnique, $uniqueFields);

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_Role')
            ->setRoleName('test_role')
            ->save();
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::delete().
     *
     * @magentoDataFixture Magento/Webapi/_files/user_with_role.php
     */
    public function testDeleteRole()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_Role')
            ->load('Test role', 'role_name')
            ->delete();
        /** @var Magento_Webapi_Model_Acl_User $user */
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Webapi_Model_Acl_User')
            ->load('test_username', 'api_key');
        $this->assertNotEmpty($user->getId());
    }
}
