<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_User
 */
class Mage_User_Model_RulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_User_Model_Rules
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_User_Model_Rules;
    }

    /**
     * Empty fixture to wrap tests in db transaction
     */
    public static function emptyFixture()
    {

    }

    /**
     * @magentoDataFixture emptyFixture
     */
    public function testCRUD()
    {
        $this->_model->setRoleType('G')
            ->setResourceId("all")
            ->setPrivileges("")
            ->setAssertId(0)
            ->setRoleId(1)
            ->setPermission('allow');

        $crud = new Magento_Test_Entity($this->_model, array('permission' => 'deny'));
        $crud->testCrud();
    }

    /**
     * @magentoDataFixture emptyFixture
     */
    public function testInitialUserPermissions()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($adapter->getTableName('admin_rule'));

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }


    /**
     * @covers Mage_user_Model_Rules::saveRel
     * @magentoDataFixture emptyFixture
     */
    public function testSetAllowForAllResources()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($adapter->getTableName('admin_rule'));

        $resources = array('all');

        $this->_model->setRoleId(1)
            ->setResources($resources)
            ->saveRel();

        $rules = $ruleSelect->query()->fetchAll();
        $this->assertEquals(1, count($rules));
        $this->assertEquals('all', $rules[0]['resource_id']);
        $this->assertEquals(1, $rules[0]['role_id']);
        $this->assertEquals('allow', $rules[0]['permission']);
    }

    /**
     * @covers Mage_user_Model_Rules::saveRel
     * @magentoDataFixture emptyFixture
     */
    public function testSetAllowForListOfResources()
    {
        $adapter = $this->_model->getResource()->getReadConnection();
        $ruleSelect = $adapter->select()
            ->from($adapter->getTableName('admin_rule'));

        $resources = array('all', 'admin');

        $this->_model->setRoleId(1)
            ->setResources($resources)
            ->saveRel();

        $rules = $ruleSelect->query()->fetchAll();

        $allowed = array();
        foreach($rules as $rule) {
            if (in_array($rule['resource_id'], $resources)) {
                $this->assertEquals('allow', $rule['permission']);
                array_push($allowed, $rule['resource_id']);
            } else {
                $this->assertEquals('deny', $rule['permission']);
            }
        }
        $this->assertEquals(0, count(array_diff($resources, $allowed)));
    }
}

