<?php
/**
 * Test for Mage_Webapi_Model_Resource_Acl_Rule.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
 */
class Mage_Webapi_Model_Resource_Acl_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Resource_Acl_Rule
     */
    protected $_ruleResource;

    protected function setUp()
    {
        $this->_objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $this->_ruleResource = $this->_objectManager->get('Mage_Webapi_Model_Resource_Acl_Rule');
    }

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::getRolesIds().
     */
    public function testGetRuleList()
    {
        /** @var Mage_Webapi_Model_Acl_Role $role */
        $role = $this->_objectManager->create('Mage_Webapi_Model_Acl_Role')->load('Test role', 'role_name');
        $allowResourceId = 'customer/get';
        $rules = $this->_ruleResource->getRuleList();
        $this->assertCount(1, $rules);
        $this->assertEquals($allowResourceId, $rules[0]['resource_id']);
        $this->assertEquals($role->getId(), $rules[0]['role_id']);
    }

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::getResourceIdsByRole().
     */
    public function testGetResourceIdsByRole()
    {
        /** @var Mage_Webapi_Model_Acl_Role $role */
        $role = $this->_objectManager->create('Mage_Webapi_Model_Acl_Role')->load('Test role', 'role_name');
        $this->assertEquals(array('customer/get'), $this->_ruleResource->getResourceIdsByRole($role->getId()));
    }
}
