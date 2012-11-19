<?php
/**
 * Test for Mage_Webapi_Model_Acl_Rule model
 *
 * @copyright {}
 *
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
        $this->_objectManager = Mage::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Mage_Webapi_Model_Acl_Role_Factory');
        $this->_model = $this->_objectManager->create('Mage_Webapi_Model_Acl_Rule');
    }

    /**
     * Cleanup model instance
     */
    protected function tearDown()
    {
        unset($this->_objectManager, $this->_model);
    }

    /**
     * Test Web API Rule CRUD
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
     * Test method Mage_Webapi_Model_Acl_Rule::saveResources()
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
