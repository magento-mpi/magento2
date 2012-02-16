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
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 global ACL role resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_RoleTest extends Magento_TestCase
{
    /**
     * Admin user data fixture
     *
     * @var Mage_Admin_Model_User
     */
    protected static $_admin;

    /**
     * API2 role data fixture
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected static $_role;

    /**
     * API2 other role data fixture
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected static $_otherRole;

    /**
     * Set admin data fixture
     *
     * @static
     */
    public static function adminDataFixture()
    {
        $data = array(
            'firstname' => 'TestAdminUserFirstName' . mt_rand(),
            'lastname'  => 'TestAdminUserLastName' . mt_rand(),
            'email'     => 'testadminuseremail' . mt_rand() . '@example.com',
            'username'  => 'TestAdminUserName' . mt_rand(),
            'password'  => '123123q'
        );

        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getModel('admin/user');
        $user->setData($data)->save();

        self::$_admin = $user;
    }

    /**
     * Set role data fixture
     *
     * @static
     */
    public static function roleDataFixture()
    {
        $data = array(
            'role_name' => 'TestRoleName' . mt_rand()
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setData($data)->save();

        self::$_role = $role;
    }

    /**
     * Set another role data fixture
     *
     * @static
     */
    public static function otherRoleDataFixture()
    {
        $data = array(
            'role_name' => 'TestOtherRoleName' . mt_rand()
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setData($data)->save();

        self::$_otherRole = $role;
    }

    /**
     * Test create new relation row of admin user to API2 role
     *
     * @magentoDataFixture adminDataFixture
     * @magentoDataFixture roleDataFixture
     */
    public function testCreateAdminToRoleRelation()
    {
        $admin = self::$_admin;
        $role = self::$_role;

        $collection = $role->getCollection()->addFilterByAdminId($admin->getId());
        $this->assertEquals(0, $collection->count());

        $role->getResource()->saveAdminToRoleRelation(self::$_admin->getId(), $role->getId());

        $collection = $role->getCollection()->addFilterByAdminId($admin->getId());
        $this->assertEquals(1, $collection->count());

        $collectionRole = $collection->getFirstItem();
        $this->assertEquals($role->getId(), $collectionRole->getId());
        $this->assertEquals($role->getRoleName(), $collectionRole->getRoleName());
        $this->assertEquals($admin->getId(), $collectionRole->getAdminId());

    }

    /**
     * Test update relation row of admin user to API2 role
     *
     * @magentoDataFixture adminDataFixture
     * @magentoDataFixture roleDataFixture
     * @magentoDataFixture otherRoleDataFixture
     */
    public function testUpdateAdminToRoleRelation()
    {
        $admin = self::$_admin;
        $role = self::$_role;

        // Create relation
        $role->getResource()->saveAdminToRoleRelation(self::$_admin->getId(), $role->getId());

        $otherRole = self::$_otherRole;

        // Update relation
        $role->getResource()->saveAdminToRoleRelation(self::$_admin->getId(), $otherRole->getId());

        $collection = $role->getCollection()->addFilterByAdminId($admin->getId());
        $collectionRole = $collection->getFirstItem();

        $this->assertEquals($otherRole->getId(), $collectionRole->getId());
        $this->assertEquals($otherRole->getRoleName(), $collectionRole->getRoleName());
        $this->assertEquals($admin->getId(), $collectionRole->getAdminId());
    }
}
