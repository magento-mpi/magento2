<?php
/**
 * Test for \Magento\Webapi\Model\Resource\Acl\Rule.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDataFixture Magento/Webapi/_files/role_with_rule.php
 */
namespace Magento\Webapi\Model\Resource\Acl;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Rule
     */
    protected $_ruleResource;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_ruleResource = $this->_objectManager->get('Magento\Webapi\Model\Resource\Acl\Rule');
    }

    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::getRolesIds().
     */
    public function testGetRuleList()
    {
        /** @var \Magento\Webapi\Model\Acl\Role $role */
        $role = $this->_objectManager->create('Magento\Webapi\Model\Acl\Role')->load('Test role', 'role_name');
        $allowResourceId = 'customer/get';
        $rules = $this->_ruleResource->getRuleList();
        $this->assertCount(1, $rules);
        $this->assertEquals($allowResourceId, $rules[0]['resource_id']);
        $this->assertEquals($role->getId(), $rules[0]['role_id']);
    }

    /**
     * Test for \Magento\Webapi\Model\Resource\Acl\Role::getResourceIdsByRole().
     */
    public function testGetResourceIdsByRole()
    {
        /** @var \Magento\Webapi\Model\Acl\Role $role */
        $role = $this->_objectManager->create('Magento\Webapi\Model\Acl\Role')->load('Test role', 'role_name');
        $this->assertEquals(array('customer/get'), $this->_ruleResource->getResourceIdsByRole($role->getId()));
    }
}
