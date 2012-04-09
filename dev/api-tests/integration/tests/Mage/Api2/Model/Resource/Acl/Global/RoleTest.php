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
     * API2 role data fixture
     *
     * @var Mage_Admin_Model_User
     */
    protected $_admin;

    /**
     * API2 role data fixture
     *
     * @var Mage_Api2_Model_Acl_Global_Role
     */
    protected $_role;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_admin = $this->_getAdminDataFixture();
        $this->_role  = $this->_getRoleDataFixture();
    }
    /**
     * Get admin data fixture
     *
     * @return Mage_Admin_Model_User
     */
    protected function _getAdminDataFixture()
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

        $this->addModelToDelete($user, true);

        return $user;
    }

    /**
     * Get role data fixture
     *
     * @return Mage_Api2_Model_Acl_Global_Role
     */
    protected function _getRoleDataFixture()
    {
        $data = array(
            'role_name' => 'TestRoleName' . mt_rand()
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setData($data)->save();

        $this->addModelToDelete($role, true);

        return $role;
    }

    /**
     * Get another role data fixture
     *
     * @return Mage_Api2_Model_Acl_Global_Role
     */
    protected function _getOtherRoleDataFixture()
    {
        $data = array(
            'role_name' => 'TestOtherRoleName' . mt_rand()
        );

        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');
        $role->setData($data)->save();

        $this->addModelToDelete($role, true);

        return $role;
    }

    /**
     * Test create new relation row of admin user to API2 role
     */
    public function testCreateAdminToRoleRelation()
    {
        $collection = $this->_role->getCollection()->addFilterByAdminId($this->_admin->getId());
        $this->assertEquals(0, $collection->count());

        $this->_role->getResource()->saveAdminToRoleRelation($this->_admin->getId(), $this->_role->getId());

        $collection = $this->_role->getCollection()->addFilterByAdminId($this->_admin->getId());
        $this->assertEquals(1, $collection->count());

        $collectionRole = $collection->getFirstItem();
        $this->assertEquals($this->_role->getId(), $collectionRole->getId());
        $this->assertEquals($this->_role->getRoleName(), $collectionRole->getRoleName());
        $this->assertEquals($this->_admin->getId(), $collectionRole->getAdminId());

    }

    /**
     * Test update relation row of admin user to API2 role
     */
    public function testUpdateAdminToRoleRelation()
    {
        // Create relation
        $this->_role->getResource()->saveAdminToRoleRelation($this->_admin->getId(), $this->_role->getId());

        $otherRole = $this->_getOtherRoleDataFixture();

        // Update relation
        $this->_role->getResource()->saveAdminToRoleRelation($this->_admin->getId(), $otherRole->getId());

        $collection = $this->_role->getCollection()->addFilterByAdminId($this->_admin->getId());
        $collectionRole = $collection->getFirstItem();

        $this->assertEquals($otherRole->getId(), $collectionRole->getId());
        $this->assertEquals($otherRole->getRoleName(), $collectionRole->getRoleName());
        $this->assertEquals($this->_admin->getId(), $collectionRole->getAdminId());
    }
}
