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
 * Test for Mage_Webapi_Model_Acl_User model
 *
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Webapi_Model_Acl_User
     */
    protected $_model;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        $this->_model = new Mage_Webapi_Model_Acl_User();
    }

    /**
     * Cleanup model instance
     */
    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * Test Web API User CRUD
     */
    public function testCRUD()
    {
        $role = new Mage_Webapi_Model_Acl_Role();
        $role->load('test_role', 'role_name');
        $this->_model
            ->setApiKey('Test User Name')
            ->setRoleId($role->getId());

        $crud = new Magento_Test_Entity($this->_model, array('api_key' => '_User_Name_'));
        $crud->testCrud();
    }
}
