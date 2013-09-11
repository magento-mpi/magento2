<?php
/**
 * Test for \Magento\Webapi\Model\Acl\Role model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Webapi/_files/role.php
 */
class Magento_Webapi_Model_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Acl\Role
     */
    protected $_model;

    /**
     * Initialize model.
     */
    protected function setUp()
    {
        $this->_objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $this->_model = $this->_objectManager->create('Magento\Webapi\Model\Acl\Role');
    }

    /**
     * Test Web API Role CRUD.
     */
    public function testCRUD()
    {
        $this->_model->setRoleName('Test Role Name');
        $crud = new Magento_TestFramework_Entity($this->_model, array('role_name' => '_Role_Name_'));
        $crud->testCrud();
    }
}
