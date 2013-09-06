<?php
/**
 * Test for Magento_Webapi_Model_Acl_User model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Webapi/_files/role.php
 */
class Magento_Webapi_Model_Acl_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Model_Acl_User
     */
    protected $_model;

    /**
     * @var Magento_Webapi_Model_Acl_Role_Factory
     */
    protected $_roleFactory;

    /**
     * Initialize model.
     */
    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Magento_Webapi_Model_Acl_Role_Factory');
        $this->_model = $this->_objectManager->create('Magento_Webapi_Model_Acl_User');
    }

    /**
     * Test Web API User CRUD.
     */
    public function testCRUD()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');

        $this->_model
            ->setApiKey('Test User Name')
            ->setContactEmail('null@null.com')
            ->setSecret('null@null.com')
            ->setRoleId($role->getId());

        $crud = new Magento_Test_Entity($this->_model, array('api_key' => '_User_Name_'));
        $crud->testCrud();
    }
}
