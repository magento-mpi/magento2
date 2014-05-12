<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model;

/**
 * @magentoAppArea adminhtml
 */
class RulesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\User\Model\Rules
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\User\Model\Rules'
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model->setRoleType(
            'G'
        )->setResourceId(
            'Magento_Adminhtml::all'
        )->setPrivileges(
            ""
        )->setAssertId(
            0
        )->setRoleId(
            1
        )->setPermission(
            'allow'
        );

        $crud = new \Magento\TestFramework\Entity($this->_model, array('permission' => 'deny'));
        $crud->testCrud();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testInitialUserPermissions()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()->from($this->_model->getResource()->getMainTable());

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('Magento_Adminhtml::all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }

    /**
     * @covers \Magento\User\Model\Rules::saveRel
     * @magentoDbIsolation enabled
     */
    public function testSetAllowForAllResources()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()->from($this->_model->getResource()->getMainTable());

        $resources = array('Magento_Adminhtml::all');

        $this->_model->setRoleId(1)->setResources($resources)->saveRel();

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('Magento_Adminhtml::all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }
}
