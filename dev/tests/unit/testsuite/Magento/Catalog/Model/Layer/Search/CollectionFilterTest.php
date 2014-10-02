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
    protected $queryFactoryMock;

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
        $this->queryFactoryMock = $this->getMock(
            '\Magento\CatalogSearch\Model\QueryFactory',
            array(),
            array(),
            '',
            false
        );
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');

        $this->model = new CollectionFilter(
            $this->catalogConfigMock, $this->queryFactoryMock, $this->storeManagerMock, $this->visibilityMock
        );
    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Search\CollectionFilter::filter
     * @covers \Magento\Catalog\Model\Layer\Search\CollectionFilter::__construct
     */
    public function testFilter()
    {
        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            array(
                'addAttributeToSelect', 'addSearchFilter', 'setStore', 'addMinimalPrice', 'addFinalPrice',
                'addTaxPercents', 'addStoreFilter', 'addUrlRewrite', 'setVisibility'
            ),
            array(),
            '',
            false
        );
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', array(), array(), '', false);
        $queryMock = $this->getMock('Magento\CatalogSearch\Helper\Query', array('getQueryText'), array(), '', false);

        $queryMock->expects($this->once())->method('getQueryText');

        $this->catalogConfigMock->expects($this->once())->method('getProductAttributes');
        $this->visibilityMock->expects($this->once())->method('getVisibleInSearchIds');
        $this->queryFactoryMock->expects($this->once())->method('getQuery')->will($this->returnValue($queryMock));
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
