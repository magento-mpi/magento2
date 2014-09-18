<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Index
     */
    protected $_index;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_session;

    /**
     * @var \Magento\TargetRule\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_targetRuleData;

    /**
     * @var \Magento\TargetRule\Model\Resource\Index|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var \Magento\TargetRule\Model\Resource\Rule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\TargetRule\Model\Resource\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collection;

    public function setUp()
    {
        $this->_storeManager = $this->_getCleanMock('\Magento\Framework\StoreManagerInterface');
        $this->_session = $this->_getCleanMock('\Magento\Customer\Model\Session');
        $this->_targetRuleData = $this->_getCleanMock('\Magento\TargetRule\Helper\Data');
        $this->_resource = $this->_getCleanMock('\Magento\TargetRule\Model\Resource\Index');
        $this->_collectionFactory = $this->getMock(
            'Magento\TargetRule\Model\Resource\Rule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->_collection = $this->getMock('\Magento\TargetRule\Model\Resource\Rule\Collection',
            ['addApplyToFilter', 'addProductFilter', 'addIsActiveFilter', 'setPriorityOrder', 'setFlag'],
            [],
            '',
            false
        );
        $this->_collection->expects($this->any())
            ->method('addApplyToFilter')
            ->will($this->returnSelf());

        $this->_collection->expects($this->any())
            ->method('addProductFilter')
            ->will($this->returnSelf());

        $this->_collection->expects($this->any())
            ->method('addIsActiveFilter')
            ->will($this->returnSelf());

        $this->_collection->expects($this->any())
            ->method('setPriorityOrder')
            ->will($this->returnSelf());

        $this->_collection->expects($this->any())
            ->method('setFlag')
            ->will($this->returnSelf());

        $this->_collectionFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_collection));

        $this->_index = (new ObjectManager($this))->getObject('\Magento\TargetRule\Model\Index',
            [
                'context' => $this->_getCleanMock('\Magento\Framework\Model\Context'),
                'registry' => $this->_getCleanMock('\Magento\Framework\Registry'),
                'ruleFactory' => $this->_collectionFactory,
                'storeManager' => $this->_storeManager,
                'session' => $this->_session,
                'targetRuleData' => $this->_targetRuleData,
                'resource' => $this->_resource,
                'resourceCollection' => $this->_getCleanMock('\Magento\Framework\Data\Collection\Db')
            ]
        );
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    public function testSetType()
    {
        $this->_index->setType(1);
        $this->assertEquals(1, $this->_index->getType());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Undefined Catalog Product List Type
     */
    public function testGetType()
    {
        $this->_index->getType();
    }

    public function testSetStoreId()
    {
        $this->_index->setStoreId(1);
        $this->assertEquals(1, $this->_index->getStoreId());
    }

    public function testGetStoreId()
    {
        $store = $this->getMock('\Magento\Store\Model\Store', array('getId', '__wakeup'), [], '', false);

        $store->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(2));

        $this->_storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($store));

        $this->assertEquals(2, $this->_index->getStoreId());
    }

    public function testSetCustomerGroupId()
    {
        $this->_index->setCustomerGroupId(1);
        $this->assertEquals(1, $this->_index->getCustomerGroupId());
    }

    public function testGetCustomerGroupId()
    {
        $this->_session->expects($this->any())
            ->method('getCustomerGroupId')
            ->will($this->returnValue(2));

        $this->assertEquals(2, $this->_index->getCustomerGroupId());
    }

    public function testSetLimit()
    {
        $this->_index->setLimit(1);
        $this->assertEquals(1, $this->_index->getLimit());
    }

    public function testGetLimit()
    {
        $this->_index->setType(1);

        $this->_targetRuleData->expects($this->any())
            ->method('getMaximumNumberOfProduct')
            ->will($this->returnValue(2));

        $this->assertEquals(2, $this->_index->getLimit());
    }

    public function testSetProduct()
    {
        $object = $this->_getCleanMock('\Magento\Framework\Object');
        $this->_index->setProduct($object);
        $this->assertEquals($object, $this->_index->getProduct());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Please define a product data object.
     */
    public function testGetProduct()
    {
        $object = $this->getMock('\Magento\Framework\Object2', [], [], '', false, false, false);
        $this->_index->setData('product', $object);
        $this->assertEquals($object, $this->_index->getProduct());
    }

    public function testSetExcludeProductIds()
    {
        $productIds = 1;
        $this->_index->setExcludeProductIds($productIds);
        $this->assertEquals(array($productIds), $this->_index->getExcludeProductIds());

        $productIds = array(1, 2);
        $this->_index->setExcludeProductIds($productIds);
        $this->assertEquals($productIds, $this->_index->getExcludeProductIds());
    }

    public function testGetExcludeProductIds()
    {
        $productIds = 1;
        $this->_index->setData('exclude_product_ids', $productIds);
        $this->assertEquals(array(), $this->_index->getExcludeProductIds());

        $productIds = array(1, 2);
        $this->_index->setData('exclude_product_ids', $productIds);
        $this->assertEquals($productIds, $this->_index->getExcludeProductIds());
    }

    public function testGetProductIds()
    {
        $productIds = array(1, 2);
        $this->_resource->expects($this->any())
            ->method('getProductIds')
            ->will($this->returnValue($productIds));

        $this->assertEquals($productIds, $this->_index->getProductIds());
    }

    public function testGetRuleCollection()
    {
        $this->_index->setType(1);
        $object = $this->_getCleanMock('\Magento\Framework\Object');
        $this->_index->setData('product', $object);
        $this->assertEquals($this->_collection, $this->_index->getRuleCollection());
    }

    public function testSelect()
    {
        $select = $this->_getCleanMock('\Magento\Framework\DB\Select');
        $this->_resource->expects($this->any())
            ->method('select')
            ->will($this->returnValue($select));

        $this->assertEquals($select, $this->_index->select());
    }
}
