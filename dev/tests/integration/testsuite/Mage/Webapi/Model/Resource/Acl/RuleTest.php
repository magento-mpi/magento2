<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Webapi_Model_Resource_Acl_Rule
 */
class Mage_Webapi_Model_Resource_Acl_RuleTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test for Mage_Webapi_Model_Resource_Acl_Role::getRolesIds()
     *
     * @magentoDataFixture Mage/Webapi/_files/role_with_rule.php
     */
    public function testGetRuleList()
    {
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Role')->load('Test role', 'role_name');
        $allowResourceId = 'customer/multiGet';
        /** @var $ruleResource Mage_Webapi_Model_Resource_Acl_Rule */
        $ruleResource = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_Rule');
        $rules = $ruleResource->getRuleList();
        $this->assertCount(1, $rules);
        $this->assertEquals($allowResourceId, $rules[0]['resource_id']);
        $this->assertEquals($role->getId(), $rules[0]['role_id']);
    }
}
