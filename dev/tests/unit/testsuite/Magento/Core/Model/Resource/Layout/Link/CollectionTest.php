<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Layout\Link;

class CollectionTest extends \Magento\Core\Model\Resource\Layout\AbstractTestCase
{
    /**
     * Name of test table
     */
    const TEST_TABLE = 'core_layout_update';

    /**
     * Name of main table alias
     *
     * @var string
     */
    protected $_tableAlias = 'update';

    /**
     * @param \Zend_Db_Select $select
     * @return \Magento\Core\Model\Resource\Layout\Link\Collection
     */
    protected function _getCollection(\Zend_Db_Select $select)
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);

        return new \Magento\Core\Model\Resource\Layout\Link\Collection(
            $eventManager,
            $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface'),
            $this->_getResource($select)
        );
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

        /** @var $connection \PHPUnit_Framework_MockObject_MockObject */
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
     * @covers \Magento\Core\Model\Resource\Layout\Link\Collection::_joinWithUpdate
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

        /** @var $resource \PHPUnit_Framework_MockObject_MockObject */
        $resource = $collection->getResource();
        $resource->expects($this->once())
            ->method('getTable')
            ->with(self::TEST_TABLE)
            ->will($this->returnValue(self::TEST_TABLE));

        $collection->addUpdatedDaysBeforeFilter(1)
            ->addUpdatedDaysBeforeFilter(2);
    }
}
