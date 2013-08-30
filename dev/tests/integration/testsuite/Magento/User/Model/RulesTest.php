<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_User_Model_RulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_User_Model_Rules
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_User_Model_Rules');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        $this->_model->setRoleType('G')
            ->setResourceId('Magento_Adminhtml::all')
            ->setPrivileges("")
            ->setAssertId(0)
            ->setRoleId(1)
            ->setPermission('allow');

        $crud = new Magento_TestFramework_Entity($this->_model, array('permission' => 'deny'));
        $crud->testCrud();
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testInitialUserPermissions()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($this->_model->getResource()->getMainTable());

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('Magento_Adminhtml::all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }

    /**
     * @covers Magento_User_Model_Rules::saveRel
     * @magentoDbIsolation enabled
     */
    public function testSetAllowForAllResources()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($this->_model->getResource()->getMainTable());

        $resources = array('Magento_Adminhtml::all');

        $this->_model->setRoleId(1)
            ->setResources($resources)
            ->saveRel();

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('Magento_Adminhtml::all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }
}

