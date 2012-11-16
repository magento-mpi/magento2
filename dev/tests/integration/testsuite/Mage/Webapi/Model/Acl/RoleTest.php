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
 * Test for Mage_Webapi_Model_Acl_Role model
 *
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Acl_Role
     */
    protected $_model;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        $this->_model = Mage::getModel('Mage_Webapi_Model_Acl_Role');
    }

    /**
     * Cleanup model instance
     */
    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Test Web API Role CRUD
     */
    public function testCRUD()
    {
        $this->_model->setRoleName('Test Role Name');
        $crud = new Magento_Test_Entity($this->_model, array('role_name' => '_Role_Name_'));
        $crud->testCrud();
    }
}
