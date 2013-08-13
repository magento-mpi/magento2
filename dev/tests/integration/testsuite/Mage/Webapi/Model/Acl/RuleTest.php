<?php
/**
 * Test for Mage_Webapi_Model_Acl_Rule model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Acl_Role_Factory
     */
    protected $_roleFactory;

    /**
     * @var Mage_Webapi_Model_Acl_Rule
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Mage_Webapi_Model_Acl_Role_Factory');
        $this->_model = $this->_objectManager->create('Mage_Webapi_Model_Acl_Rule');
    }

    /**
     * Test Web API Role CRUD.
     */
    public function testCRUD()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');
        $allowResourceId = 'customer/multiGet';

        $this->_model->setRoleId($role->getId())
            ->setResourceId($allowResourceId);

        $crud = new Magento_Test_Entity($this->_model, array('resource_id' => 'customer/get'));
        $crud->testCrud();
    }

    /**
     * Test Mage_Webapi_Model_Acl_Rule::saveResources() method.
     */
    public function testSaveResources()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');
        $resources = array('customer/create', 'customer/update');

        $this->_model
            ->setRoleId($role->getId())
            ->setResources($resources)
            ->saveResources();

        /** @var $rulesSet Mage_Webapi_Model_Resource_Acl_Rule_Collection */
        $rulesSet = $this->_objectManager->get('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
            ->getByRole($role->getRoleId())->load();
        $this->assertCount(2, $rulesSet);
    }
}
