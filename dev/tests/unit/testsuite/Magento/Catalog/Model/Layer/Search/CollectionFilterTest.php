<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Search;

class CollectionFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $visibilityMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $catalogConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Catalog\Model\Layer\Search\CollectionFilter
     */
    protected $model;

    protected function setUp()
    {
        $this->visibilityMock = $this->getMock('Magento\Catalog\Model\Product\Visibility', array(), array(), '', false);
        $this->catalogConfigMock = $this->getMock('\Magento\Catalog\Model\Config', array(), array(), '', false);
        $this->helperMock = $this->getMock('\Magento\CatalogSearch\Helper\Data', array(), array(), '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManagerInterface');

        $this->model = new CollectionFilter(
            $this->catalogConfigMock, $this->helperMock, $this->storeManagerMock, $this->visibilityMock
        );
    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Search\CollectionFilter::filter
     * @covers \Magento\Catalog\Model\Layer\Search\CollectionFilter::__construct
     */
    public function testFilter()
    {
        $collectionMock = $this->getMock(
            '\Magento\Search\Model\Resource\Collection', array(), array(), '', false
        );
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', array(), array(), '', false);
        $queryMock = $this->getMock('Magento\CatalogSearch\Helper\Query', array('getQueryText'), array(), '', false);

        $queryMock->expects($this->once())->method('getQueryText');

        $this->catalogConfigMock->expects($this->once())->method('getProductAttributes');
        $this->visibilityMock->expects($this->once())->method('getVisibleInSearchIds');
        $this->helperMock->expects($this->once())->method('getQuery')->will($this->returnValue($queryMock));
        $this->storeManagerMock->expects($this->once())->method('getStore');

        $collectionMock->expects($this->once())->method('addAttributeToSelect')
            ->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addSearchFilter')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('setStore')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addMinimalPrice')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addFinalPrice')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addTaxPercents')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addStoreFilter')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addUrlRewrite')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('setVisibility')->will($this->returnValue($collectionMock));

        $this->model->filter($collectionMock, $categoryMock);
    }
}
