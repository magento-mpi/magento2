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

class Mage_Core_Model_Resource_Layout_Update_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test 'where' condition for assertion
     */
    const TEST_WHERE_CONDITION = 'condition = 1';

    /**
     * Test interval in days
     */
    const TEST_DAYS_BEFORE = 3;

    /**
     * @var Mage_Core_Model_Resource_Layout_Update_Collection
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
            0 => array('main_table.updated_at', array('notnull' => true)),
            1 => array('main_table.updated_at', array('lt' => 'date')),
        )
    );

    /**
     * Retrieve layout update collection instance
     *
     * @param Zend_Db_Select $select
     * @return Mage_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _getCollection(Zend_Db_Select $select)
    {
        $connection = $this->getMock('Varien_Db_Adapter_Pdo_Mysql',
            array(), array(), '', false
        );
        $connection->expects($this->once())
            ->method('select')
            ->will($this->returnValue($select));
        $connection->expects($this->any())
            ->method('quoteIdentifier')
            ->will($this->returnArgument(0));

        $resource = $this->getMockForAbstractClass('Mage_Core_Model_Resource_Db_Abstract', array(), '', false, true,
            true, array('getReadConnection', 'getMainTable', 'getTable'));
        $resource->expects($this->any())
            ->method('getReadConnection')
            ->will($this->returnValue($connection));
        $resource->expects($this->any())
            ->method('getTable')
            ->will($this->returnArgument(0));

        $this->_collection = new Mage_Core_Model_Resource_Layout_Update_Collection($resource);
        return $this->_collection;
    }

    protected function tearDown()
    {
        unset($this->_collection);
    }

    public function testAddThemeFilter()
    {
        $themeId = 1;
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with('link.theme_id = ?', $themeId);

        $collection = $this->_getCollection($select);
        $collection->addThemeFilter($themeId);
    }

    public function testAddStoreFilter()
    {
        $storeId = 1;
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with('link.store_id = ?', $storeId);

        $collection = $this->_getCollection($select);
        $collection->addStoreFilter($storeId);
    }

    /**
     * @covers Mage_Core_Model_Resource_Layout_Update_Collection::_joinWithLink
     */
    public function testJoinWithLink()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('join')
            ->with(
                array('link' => 'core_layout_link'),
                'link.layout_update_id = main_table.layout_update_id',
                $this->isType('array')
            );

        $collection = $this->_getCollection($select);
        $collection->addStoreFilter(1);
        $collection->addThemeFilter(1);
    }

    public function testAddNoLinksFilter()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('joinLeft')
            ->with(
                array('link' => 'core_layout_link'),
                'link.layout_update_id = main_table.layout_update_id',
                array(array())
            );
        $select->expects($this->once())
            ->method('where')
            ->with(self::TEST_WHERE_CONDITION);

        $collection = $this->_getCollection($select);

        /** @var $connection PHPUnit_Framework_MockObject_MockObject */
        $connection = $collection->getResource()->getReadConnection();
        $connection->expects($this->once())
            ->method('prepareSqlCondition')
            ->with('link.layout_update_id', array('null' => true))
            ->will($this->returnValue(self::TEST_WHERE_CONDITION));

        $collection->addNoLinksFilter();
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
