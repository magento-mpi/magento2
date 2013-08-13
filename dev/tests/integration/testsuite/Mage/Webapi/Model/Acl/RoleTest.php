<?php
/**
 * Test for Mage_Webapi_Model_Acl_Role model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Acl_Role
     */
    protected $_model;

    /**
     * Initialize model.
     */
    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Mage_Webapi_Model_Acl_Role');
    }

    /**
     * Test Web API Role CRUD.
     */
    public function testCRUD()
    {
        $this->_model->setRoleName('Test Role Name');
        $crud = new Magento_Test_Entity($this->_model, array('role_name' => '_Role_Name_'));
        $crud->testCrud();
    }
}
