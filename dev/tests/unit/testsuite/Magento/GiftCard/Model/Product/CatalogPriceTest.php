<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Product;


class CatalogPriceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\GiftCard\Model\Product\CatalogPrice
     */
    protected $catalogPrice;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    protected function setUp()
    {
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->catalogPrice = new \Magento\GiftCard\Model\Product\CatalogPrice();
    }

    public function testGetCatalogPrice()
    {
        $priceModelMock
            = $this->getMock('Magento\Catalog\Model\Product\Type\Price', array('getMinAmount'), array(), '', false);
        $this->productMock
            ->expects($this->once())
            ->method('getPriceModel')
            ->will($this->returnValue($priceModelMock));
        $priceModelMock
            ->expects($this->once())
            ->method('getMinAmount')
            ->with($this->productMock)
            ->will($this->returnValue(15));
        $this->assertEquals(15, $this->catalogPrice->getCatalogPrice($this->productMock));
    }

    public function testGetCatalogRegularPrice()
    {
        $this->assertEquals(null, $this->catalogPrice->getCatalogRegularPrice($this->productMock));
    }
}

