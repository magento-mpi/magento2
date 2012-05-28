<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test model admin api role controller
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Api2_Adminhtml_Api2_RoleControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_urlModel;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

       $this->_urlModel = Mage::getSingleton('Mage_Adminhtml_Model_Url');
    }
    /**
     * Test role correctly saved
     */
    public function testRoleSaveCreate()
    {
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
        $roleName = uniqid('role_');


        try {
            $this->loginToAdmin();
            $this->getRequest()->setParams(array(
                'role_name' => $roleName,
                'id'        => $role->getId(),
                'key'       => $this->_urlModel->getSecretKey()
            ));

            $this->dispatch('admin/api2_role/save');
        } catch (Exception $e) {
            throw $e;
        }

        $role->load($roleName, 'role_name');
        $this->assertTrue($role->getId()>0);

        $role->delete();
    }

    /**
     * Test role correctly saved
     */
    public function testRoleSaveUpdate()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();
        $this->setFixture('role', $role);

        $roleName2 = uniqid('role_');

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParams(array(
                'role_name' => $roleName2,
                'id'        => $role->getId(),
                'key'       => $this->_urlModel->getSecretKey()
            ));

            $this->dispatch('admin/api2_role/save');
        } catch (Exception $e) {
            throw $e;
        }

        $role->load($role->getId());

        $this->assertEquals($roleName2, $role->getRoleName());
    }

    /**
     * Test role is rendered
     */
    public function testRoleGrid()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();
        $this->setFixture('role', $role);
        $this->addModelToDelete($role, true);

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParams(array(
                'role_name'  => $roleName,
                'key'        => $this->_urlModel->getSecretKey()
            ));

            $this->dispatch('admin/api2_role');
        } catch (Exception $e) {
            throw $e;
        }

        $this->assertContains($roleName, $this->getResponse()->getBody());
    }

    /**
     * Test role is deleted correctly
     */
    public function testRoleDelete()
    {
        //generate test item
        /** @var $role Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
        $roleName = uniqid('role_');
        $role->setRoleName($roleName)->save();

        try {
            $this->loginToAdmin();
            $this->getRequest()->setParams(array(
                'id'  => $role->getId(),
                'key' => $this->_urlModel->getSecretKey()
            ));

            $this->dispatch('admin/api2_role/delete');
        } catch (Exception $e) {
            throw $e;
        }

        /** @var $role2 Mage_Api2_Model_Acl_Global_Role */
        $role2 = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');

        $role2->load($role->getId());
        $this->assertEmpty($role2->getId());
    }
}
