<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_SalesRule_Model_Resource_Report_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test table name
     */
    const TABLE_NAME = 'test';

    /**
     * List of test rules;
     *
     * @var array
     */
    protected $_rules = array(
        array('rule_name' => 'test1'),
        array('rule_name' => 'test2'),
        array('rule_name' => 'test3')
    );

    public function testGetUniqRulesNamesList()
    {
        $dbAdapterMock = $this->getMockForAbstractClass('Zend_Db_Adapter_Abstract', array(), '', false);
        $select = $this->getMock('Magento_DB_Select', array('from'), array($dbAdapterMock));
        $select->expects($this->once())
            ->method('from')
            ->with(self::TABLE_NAME, $this->isInstanceOf('Zend_Db_Expr'))
            ->will($this->returnValue($select));

        $adapterMock = $this->getMock('Magento_DB_Adapter_Pdo_Mysql', array('select', 'fetchAll'), array(), '', false);
        $adapterMock->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));
        $adapterMock->expects($this->once())
            ->method('fetchAll')
            ->with($select)
            ->will($this->returnCallback(array($this, 'fetchAllCallback')));

        $resourceMock = $this->getMock('Magento_Core_Model_Resource',
            array('getConnection', 'getTableName'), array(), '', false
        );
        $resourceMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($adapterMock));
        $resourceMock->expects($this->once())
            ->method('getTableName')
            ->will($this->returnValue(self::TABLE_NAME));

        $model = new Magento_SalesRule_Model_Resource_Report_Rule($resourceMock);

        $expectedRuleNames = array();
        foreach ($this->_rules as $rule) {
            $expectedRuleNames[] = $rule['rule_name'];
        }
        $this->assertEquals($expectedRuleNames, $model->getUniqRulesNamesList());
    }

    /**
     * Check structure of sql query
     *
     * @param Magento_DB_Select $select
     * @return array
     */
    public function fetchAllCallback(Magento_DB_Select $select)
    {
        $whereParts = $select->getPart(Magento_DB_Select::WHERE);
        $this->assertCount(2, $whereParts);
        $this->assertContains("rule_name IS NOT NULL", $whereParts[0]);
        $this->assertContains("rule_name <> ''", $whereParts[1]);

        $orderParts = $select->getPart(Magento_DB_Select::ORDER);
        $this->assertCount(1, $orderParts);
        $expectedOrderParts = array('rule_name', 'ASC');
        $this->assertEquals($expectedOrderParts, $orderParts[0]);

        return $this->_rules;
    }
}
