<?php
/**
 * Test for Magento_Webapi_Model_Resource_Acl_Rule.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
 */
class Magento_Webapi_Model_Resource_Acl_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Model_Resource_Acl_Rule
     */
    protected $_ruleResource;

    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_ruleResource = $this->_objectManager->get('Magento_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::getRolesIds().
     */
    public function testGetRuleList()
    {
        /** @var Magento_Webapi_Model_Acl_Role $role */
        $role = $this->_objectManager->create('Magento_Webapi_Model_Acl_Role')->load('Test role', 'role_name');
        $allowResourceId = 'customer/get';
        $rules = $this->_ruleResource->getRuleList();
        $this->assertCount(1, $rules);
        $this->assertEquals($allowResourceId, $rules[0]['resource_id']);
        $this->assertEquals($role->getId(), $rules[0]['role_id']);
    }

    /**
     * Test for Magento_Webapi_Model_Resource_Acl_Role::getResourceIdsByRole().
     */
    public function testGetResourceIdsByRole()
    {
        /** @var Magento_Webapi_Model_Acl_Role $role */
        $role = $this->_objectManager->create('Magento_Webapi_Model_Acl_Role')->load('Test role', 'role_name');
        $this->assertEquals(array('customer/get'), $this->_ruleResource->getResourceIdsByRole($role->getId()));
    }
}
