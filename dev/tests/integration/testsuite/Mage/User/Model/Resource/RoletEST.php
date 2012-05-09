<?php

class Mage_User_Model_Resource_RoleTest extends PHPUnit_Framework_TestCase
{
    public function testGetRoleUsers()
    {
        $role = new Mage_User_Model_Role();
        $roleResource = new Mage_User_Model_Resource_Role();

        $this->assertEmpty($roleResource->getRoleUsers($role));

        $role->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');
        $this->assertNotEmpty($roleResource->getRoleUsers($role));
    }
}
