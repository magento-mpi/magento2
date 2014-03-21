<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Pricing\Price;

use \Magento\Pricing\PriceInfo\Base;

/**
 * Class MsrpPriceTest
 */
class MsrpPriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Pricing\Price\MsrpPrice
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product;

    protected function setUp()
    {
        $this->product = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['getPriceInfo', '__wakeup'],
            [],
            '',
            false
        );

        $priceInfo = $this->getMock(
            '\Magento\Pricing\PriceInfo',
            ['getAdjustments'],
            [],
            '',
            false
        );

        $priceInfo->expects($this->any())
            ->method('getAdjustments')
            ->will($this->returnValue([]));

        $this->product->expects($this->any())
            ->method('getPriceInfo')
            ->will($this->returnValue($priceInfo));

        $this->helper = $this->getMock(
            '\Magento\Catalog\Helper\Data',
            ['isShowPriceOnGesture', 'getMsrpPriceMessage', 'isMsrpEnabled'],
            [],
            '',
            false
        );

        $this->object = new MsrpPrice($this->product, $this->helper, Base::PRODUCT_QUANTITY_DEFAULT);
    }

    public function testIsShowPriceOnGestureTrue()
    {
        $this->helper->expects($this->once())
            ->method('isShowPriceOnGesture')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(true));

        $this->assertTrue($this->object->isShowPriceOnGesture());
    }

    public function testIsShowPriceOnGestureFalse()
    {
        $this->helper->expects($this->once())
            ->method('isShowPriceOnGesture')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue(false));

        $this->assertFalse($this->object->isShowPriceOnGesture());
    }

    public function testGetMsrpPriceMessage()
    {
        $expectedMessage = 'test';
        $this->helper->expects($this->once())
            ->method('getMsrpPriceMessage')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($expectedMessage));

        $this->assertEquals($expectedMessage, $this->object->getMsrpPriceMessage());
    }

    public function testIsMsrpEnabled()
    {
        $this->helper->expects($this->once())
            ->method('isMsrpEnabled')
            ->will($this->returnValue(true));

        $this->assertTrue($this->object->isMsrpEnabled());
    }
}
