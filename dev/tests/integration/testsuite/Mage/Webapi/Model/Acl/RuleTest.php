<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Model_Acl_Rule model
 *
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Acl_Rule
     */
    protected $_model;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        $this->_model = new Mage_Webapi_Model_Acl_Rule();
    }

    /**
     * Cleanup model instance
     */
    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Test Web API Rule CRUD
     *
     * @magentoDataFixture Mage/Webapi/_files/role.php
     */
    public function testCRUD()
    {
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load('test_role', 'role_name');
        $allowResourceId = 'customer/multiGet';

        $this->_model->setRoleId($role->getId())
            ->setResourceId($allowResourceId);

        $crud = new Magento_Test_Entity($this->_model, array('resource_id' => 'customer/get'));
        $crud->testCrud();
    }

    /**
     * Test method Mage_Webapi_Model_Acl_Rule::SaveResources()
     *
     * @magentoDataFixture Mage/Webapi/_files/role.php
     */
    public function testSaveResources()
    {
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load('test_role', 'role_name');
        $resources = array('customer/create', 'customer/update');

        $this->_model
            ->setRoleId($role->getId())
            ->setResources($resources)
            ->saveResources();

        /** @var $rulesSet Mage_Webapi_Model_Resource_Acl_Rule_Collection */
        $rulesSet = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule_Collection')
            ->getByRoles($role->getRoleId())->load();
        $this->assertEquals(2, $rulesSet->count());
    }
}
