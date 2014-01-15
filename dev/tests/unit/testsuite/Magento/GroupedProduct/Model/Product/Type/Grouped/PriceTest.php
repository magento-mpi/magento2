<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\Product\Type\Grouped;

class PriceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\GroupedProduct\Model\Product\Type\Grouped\Price
     */
    protected $finalPrice;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
//        $methods = array('getCalculatedFinalPrice', 'getPrice', 'setFinalPrice', 'getData', 'hasCustomOptions', 'getTypeInstance',  'getCustomOption', '__wakeup');
//        $methods = array('getId', 'getFinalPrice', '__wakeup', 'getCalculatedFinalPrice', 'getPrice', 'setFinalPrice', 'getData', 'hasCustomOptions', 'getTypeInstance',  'getCustomOption', 'getGroupPrice', 'getTierPrice', 'getStore');
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->optionMock =
           $this->getMock('Magento\Catalog\Model\Product\Option', array('getValue', '__wakeup'), array(), '', false);
        $this->finalPrice = $helper->getObject('Magento\GroupedProduct\Model\Product\Type\Grouped\Price', array(
            )
        );
    }

    public function testGetFinalPriceIfQtyIsNullAndFinalPriceExist()
    {
        $finalPrice = 15;
        $this->productMock
            ->expects($this->any())
            ->method('getCalculatedFinalPrice')
            ->will($this->returnValue($finalPrice));
        $this->assertEquals($finalPrice, $this->finalPrice->getFinalPrice(null, $this->productMock));
    }

    /**
     * @param $option
     * @dataProvider getFinalPriceDataProvider
     */
    public function testGetFinalPrice($option)
    {
        $finalPrice = 15;
        $this->productMock
            ->expects($this->any())
            ->method('getCalculatedFinalPrice')
            ->will($this->returnValue($finalPrice));
        //mock for parent class getFinalPrice

        $this->productMock->expects($this->any())->method('getPrice')->will($this->returnValue(10));
        $this->productMock
            ->expects($this->any())
            ->method('setFinalPrice')
            ->with(10)
            ->will($this->returnValue($this->productMock));
        $this->productMock->expects($this->any())->method('getData')->will($this->returnValue(10));

        //test method
        $this->productMock->expects($this->once())->method('hasCustomOptions')->will($this->returnValue(true));
        $productTypeMock = $this->getMock(
            'Magento\GroupedProduct\Model\Product\Type\Grouped', array(), array(), '', false);
        $this->productMock->expects($this->any())->method('getStore')->will($this->returnValue('hjh'));
        $this->productMock
            ->expects($this->once())
            ->method('getTypeInstance')
            ->will($this->returnValue($productTypeMock));
        $productTypeMock->expects($this->once())->method('setStoreFilter')->will($this->returnValue($productTypeMock));

        $methods = array('getId', 'getFinalPrice', '__wakeup');
        $childProductMock =
            $this->getMock('Magento\Catalog\Model\Product', $methods, array(), '', false);
        $productTypeMock
            ->expects($this->once())->method('getAssociatedProducts')
            ->with($this->productMock)
            ->will($this->returnValue(array($childProductMock)));
        $childProductMock->expects($this->any())->method('getId')->will($this->returnValue('id'));
        $this->productMock
            ->expects($this->any())
            ->method('getCustomOption')
            ->will($this->returnValueMap(array(array('associated_product_id', $option))));
        $this->optionMock->expects($this->any())->method('getValue')->will($this->returnValue(10));
        $this->productMock->expects($this->any())->method('getFinalPrice')->with(110)->will($this->returnValue(110));

        $this->assertEquals(10, $this->finalPrice->getFinalPrice(100, $this->productMock));
    }

    public function getFinalPriceDataProvider()
    {
        return array(
            'custom_option_null' => array(null, null),
            'custom_option_exist' => array($this->optionMock)
        );
    }
}
