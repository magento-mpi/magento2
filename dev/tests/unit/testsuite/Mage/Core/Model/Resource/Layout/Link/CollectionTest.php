<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Layout_Link_CollectionTest extends PHPUnit_Framework_TestCase
{
    const TEST_WHERE_CONDITION = 'test';
    const TEST_TABLE           = 'core_layout_update';
    const TEST_DAYS_BEFORE     = 3;

    /**
     * @var Mage_Core_Model_Resource_Layout_Link_Collection
     */
    protected $_collection;

    /**
     * Expected conditions for testAddUpdatedDaysBeforeFilter
     *
     * @var array
     */
    protected $_expectedConditions = array(
        'counter' => 0,
        'data'    => array(
            0 => array('update.updated_at', array('notnull' => true)),
            1 => array('update.updated_at', array('lt' => 'date')),
        )
    );

    /**
     * Retrieve layout update collection instance
     *
     * @param Zend_Db_Select $select
     * @return Mage_Core_Model_Resource_Layout_Link_Collection
     */
    protected function _getCollection(Zend_Db_Select $select)
    {
        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array('select'), '', false);
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));
        $connection->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $resource = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract', array(), '', false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable', 'quoteIdentifier'));
        $resource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));

        $this->_collection = new Mage_Core_Model_Resource_Layout_Link_Collection($resource);
        return $this->_collection;
    }

    /**
     * @dataProvider filterFlagDataProvider
     * @param bool $flag
     */
    public function testAddTemporaryFilter($flag)
    {
        $select = $this->getMock('Zend_Db_Select', array(), array('where'), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with(self::TEST_WHERE_CONDITION);

        $collection = $this->_getCollection($select);

        /** @var $connection PHPUnit_Framework_MockObject_MockObject */
        $connection = $collection->getResource()->getReadConnection();
        $connection->expects($this->any())
            ->method('prepareSqlCondition')
            ->with('main_table.is_temporary', $flag)
            ->will($this->returnValue(self::TEST_WHERE_CONDITION));

        $collection->addTemporaryFilter($flag);
    }

    /**
     * @return array
     */
    public function filterFlagDataProvider()
    {
        return array(
            'Add temporary filter'     => array('$flag' => true),
            'Disable temporary filter' => array('$flag' => false),
        );
    }

    /**
     * @covers Mage_Core_Model_Resource_Layout_Link_Collection::_joinWithUpdate
     */
    public function testJoinWithUpdate()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('join')
            ->with(
                array('update' => self::TEST_TABLE),
                'update.layout_update_id = main_table.layout_update_id',
                $this->isType('array')
            );

        $collection = $this->_getCollection($select);

        /** @var $resource PHPUnit_Framework_MockObject_MockObject */
        $resource = $collection->getResource();
        $resource->expects($this->once())
            ->method('getTable')
            ->with(self::TEST_TABLE)
            ->will($this->returnValue(self::TEST_TABLE));

        $collection->addUpdatedDaysBeforeFilter(1)
            ->addUpdatedDaysBeforeFilter(2);
    }

    public function testAddUpdatedDaysBeforeFilter()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->any())
            ->method('where')
            ->with(self::TEST_WHERE_CONDITION);

        $collection = $this->_getCollection($select);

        /** @var $connection PHPUnit_Framework_MockObject_MockObject */
        $connection = $collection->getResource()->getReadConnection();
        $connection->expects($this->any())
            ->method('prepareSqlCondition')
            ->will($this->returnCallback(array($this, 'verifyPrepareSqlCondition')));

        // expected date without time
        $datetime = new DateTime();
        $storeInterval = new DateInterval('P' . self::TEST_DAYS_BEFORE . 'D');
        $datetime->sub($storeInterval);
        $expectedDate = Varien_Date::formatDate($datetime->getTimestamp());
        $this->_expectedConditions['data'][1][1]['lt'] = $expectedDate;

        $collection->addUpdatedDaysBeforeFilter(self::TEST_DAYS_BEFORE);
    }

    /**
     * Assert SQL condition
     *
     * @param string $fieldName
     * @param array $condition
     * @return string
     */
    public function verifyPrepareSqlCondition($fieldName, $condition)
    {
        $counter = $this->_expectedConditions['counter'];
        $data = $this->_expectedConditions['data'][$counter];
        $this->_expectedConditions['counter']++;

        $this->assertEquals($data[0], $fieldName);

        $this->assertCount(1, $data[1]);
        $key   = array_keys($data[1]);
        $key   = reset($key);
        $value = reset($data[1]);

        $this->assertArrayHasKey($key, $condition);

        if ($key == 'lt') {
            $this->assertContains($value, $condition[$key]);
        } else {
            $this->assertContains($value, $condition);
        }

        return self::TEST_WHERE_CONDITION;
    }
}
