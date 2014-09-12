<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Search;

class FilterableAttributeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Search\FilterableAttributeList
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layerMock;

    protected function setUp()
    {
        $this->collectionFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory', array('create'), array(), '', false);

        $this->storeManagerMock = $this->getMock(
            '\Magento\Framework\StoreManagerInterface', array(), array(), '', false
        );

        $this->layerMock = $this->getMock(
            '\Magento\Catalog\Model\Layer\Search', array(), array(), '', false
        );

        $this->model = new \Magento\Catalog\Model\Layer\Search\FilterableAttributeList(
            $this->collectionFactoryMock,
            $this->storeManagerMock,
            $this->layerMock
        );

    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Search\FilterableAttributeList::_prepareAttributeCollection()
     */
    public function testGetList()
    {
        $productCollectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false
        );
        $this->layerMock->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollectionMock));
        $setIds = array(2, 3, 5);
        $productCollectionMock->expects($this->once())->method('getSetIds')->will($this->returnValue($setIds));

        $storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $storeId = 4321;
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));

        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\Collection', array(), array(), '', false
        );
        $this->collectionFactoryMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collectionMock));

        $collectionMock
            ->expects($this->once())
            ->method('setItemObjectClass')
            ->with('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('setAttributeSetFilter')
            ->with($setIds)
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('addStoreLabel')
            ->will($this->returnSelf());
        $collectionMock
            ->expects($this->once())
            ->method('setOrder');

        $collectionMock->expects($this->once())->method('addIsFilterableInSearchFilter')->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('addVisibleFilter')->will($this->returnSelf());
        $collectionMock->expects($this->once())->method('load');

        $this->assertEquals($collectionMock, $this->model->getList());
    }
}
