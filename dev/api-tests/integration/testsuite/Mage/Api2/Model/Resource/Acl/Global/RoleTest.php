<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
        $user = Mage::getModel('Mage_Admin_Model_User');
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
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
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
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
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
