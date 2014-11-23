<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Model\Resource;

class SegmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment
     */
    protected $_resourceModel;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_writeAdapter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configShare;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_conditions;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_segment;

    protected function setUp()
    {
        $this->_writeAdapter = $this->getMockForAbstractClass(
            'Magento\Framework\DB\Adapter\AdapterInterface',
            array(),
            '',
            false,
            true,
            true,
            array('query', 'insertMultiple', 'beginTransaction', 'commit')
        );

        $this->_resource = $this->getMock(
            'Magento\Framework\App\Resource',
            array(),
            array(),
            '',
            false
        );
        $this->_resource->expects($this->any())->method('getTableName')->will($this->returnArgument(0));
        $this->_resource->expects(
            $this->once()
        )->method(
            'getConnection'
        )->with()->will(
            $this->returnValueMap(array(array('core_write', $this->_writeAdapter)))
        );

        $this->_configShare = $this->getMock(
            'Magento\Customer\Model\Config\Share',
            array('isGlobalScope', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_segment = $this->getMock(
            'Magento\CustomerSegment\Model\Segment',
            array('getConditions', 'getWebsiteIds', 'getId', '__wakeup'),
            array(),
            '',
            false
        );

        $this->_conditions = $this->getMock(
            'Magento\CustomerSegment\Model\Segment\Condition\Combine\Root',
            array('getConditionsSql', 'getConditions'),
            array(),
            '',
            false
        );

        $this->_resourceModel = new \Magento\CustomerSegment\Model\Resource\Segment(
            $this->_resource,
            $this->getMock('Magento\CustomerSegment\Model\Resource\Helper', array(), array(), '', false),
            $this->_configShare,
            $this->getMock('Magento\Framework\Stdlib\DateTime', null, array(), '', true)
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testSaveCustomersFromSelect()
    {
        $select =
            $this->getMock('Magento\Framework\DB\Select', array('joinLeft', 'from', 'columns'), array(), '', false);
        $this->_segment->expects($this->any())->method('getId')->will($this->returnValue(3));
        $statement = $this->getMock(
            'Zend_Db_Statement',
            array('closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount'),
            array(),
            '',
            false
        );
        $websites = array(8, 9);
        $statement->expects(
            $this->at(0)
        )->method(
            'fetch'
        )->will(
            $this->returnValue(array('entity_id' => 4, 'website_id' => $websites[0]))
        );
        $statement->expects(
            $this->at(1)
        )->method(
            'fetch'
        )->will(
            $this->returnValue(array('entity_id' => 5, 'website_id' => $websites[1]))
        );
        $statement->expects($this->at(2))->method('fetch')->will($this->returnValue(false));
        $this->_writeAdapter->expects(
            $this->any()
        )->method(
            'query'
        )->with(
            $this->equalTo($select)
        )->will(
            $this->returnValue($statement)
        );
        $callback = function ($data) use ($websites) {
            foreach ($data as $item) {
                if (!isset($item['website_id']) || !in_array($item['website_id'], $websites)) {
                    return false;
                }
            }
            return true;
        };

        $this->_writeAdapter->expects(
            $this->once()
        )->method(
            'insertMultiple'
        )->with(
            $this->equalTo('magento_customersegment_customer'),
            $this->callback($callback)
        );
        $this->_writeAdapter->expects($this->once())->method('beginTransaction');
        $this->_writeAdapter->expects($this->once())->method('commit');

        $this->_resourceModel->saveCustomersFromSelect($this->_segment, $select);
    }

    /**
     * @dataProvider aggregateMatchedCustomersDataProvider
     * @param bool $scope
     * @param array $websites
     * @param mixed $websiteIds
     */
    public function testAggregateMatchedCustomersOneWebsite($scope, $websites, $websiteIds)
    {
        $select = $this->getMock(
            'Magento\Framework\DB\Select',
            array('joinLeft', 'from', 'columns'),
            array(),
            '',
            false
        );
        $this->_conditions->expects(
            $this->once()
        )->method(
            'getConditionsSql'
        )->with(
            $this->isNull(),
            $this->equalTo($websiteIds)
        )->will(
            $this->returnValue($select)
        );
        $this->_segment->expects($this->once())->method('getConditions')->will($this->returnValue($this->_conditions));
        $this->_segment->expects($this->once())->method('getWebsiteIds')->will($this->returnValue($websites));
        $this->_segment->expects($this->any())->method('getId')->will($this->returnValue(3));
        $statement = $this->getMock(
            'Zend_Db_Statement',
            array('closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount'),
            array(),
            '',
            false
        );
        $statement->expects(
            $this->at(0)
        )->method(
            'fetch'
        )->will(
            $this->returnValue(array('entity_id' => 4, 'website_id' => $websites[0]))
        );
        $statement->expects($this->at(1))->method('fetch')->will($this->returnValue(false));
        $this->_writeAdapter->expects(
            $this->any()
        )->method(
            'query'
        )->with(
            $this->equalTo($select)
        )->will(
            $this->returnValue($statement)
        );
        $callback = function ($data) use ($websites) {
            return isset($data[0]['website_id']) && $data[0]['website_id'] === $websites[0];
        };
        $this->_writeAdapter->expects(
            $this->once()
        )->method(
            'insertMultiple'
        )->with(
            $this->equalTo('magento_customersegment_customer'),
            $this->callback($callback)
        );
        $this->_writeAdapter->expects($this->exactly(2))->method('beginTransaction');
        $this->_writeAdapter->expects($this->exactly(2))->method('commit');

        $this->_configShare->expects($this->any())->method('isGlobalScope')->will($this->returnValue($scope));

        $this->_resourceModel->aggregateMatchedCustomers($this->_segment);
    }

    public function aggregateMatchedCustomersDataProvider()
    {
        return array(array(true, array(7), array(7)), array(false, array(6), 6));
    }
}
