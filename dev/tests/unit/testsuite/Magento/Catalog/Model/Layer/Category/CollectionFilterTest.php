<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Category;

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
     * @var \Magento\Catalog\Model\Layer\Category\CollectionFilter
     */
    protected $model;

    protected function setUp()
    {
        $this->visibilityMock = $this->getMock(
            '\Magento\Catalog\Model\Product\Visibility', array(), array(), '', false
        );
        $this->catalogConfigMock = $this->getMock('\Magento\Catalog\Model\Config', array(), array(), '', false);
        $this->model = new CollectionFilter($this->visibilityMock, $this->catalogConfigMock);
    }

    /**
     * @covers \Magento\Catalog\Model\Layer\Category\CollectionFilter::filter
     * @covers \Magento\Catalog\Model\Layer\Category\CollectionFilter::__construct
     */
    public function testFilter()
    {
        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection', array(), array(), '', false
        );

        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', array(), array(), '', false);
        $categoryMock->expects($this->once())->method('getId');

        $this->catalogConfigMock->expects($this->once())->method('getProductAttributes');
        $this->visibilityMock->expects($this->once())->method('getVisibleInCatalogIds');

        $collectionMock->expects($this->once())->method('addAttributeToSelect')
            ->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addMinimalPrice')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addFinalPrice')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addTaxPercents')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('addUrlRewrite')->will($this->returnValue($collectionMock));
        $collectionMock->expects($this->once())->method('setVisibility')->will($this->returnValue($collectionMock));

        $this->model->filter($collectionMock, $categoryMock);
    }
}
