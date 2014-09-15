<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Shipping;

use Magento\Framework\Object;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    const SUBTOTAL = 10;

    /**
     * @var \Magento\Checkout\Block\Shipping\Price
     */
    protected $priceObj;

    /**
     * @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quote;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $store;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['convertPrice', '__wakeup'])
            ->getMock();

        $this->quote = $this->getMockBuilder('Magento\Sales\Model\Quote')
            ->disableOriginalConstructor()
            ->setMethods(['getStore', '__wakeup'])
            ->getMock();

        $this->quote->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->store));

        $checkoutSession = $this->getMockBuilder('\Magento\Checkout\Model\Session')
            ->disableOriginalConstructor()
            ->setMethods(['getQuote', '__wakeup'])
            ->getMock();

        $checkoutSession->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($this->quote));

        $this->priceObj = $objectManager->getObject(
            '\Magento\Checkout\Block\Shipping\Price',
            ['checkoutSession' => $checkoutSession]
        );
    }

    public function testGetShippingPrice()
    {
        $shippingPrice = 5;
        $convertedPrice = "$5";

        $shippingRateMock = $this->getMockBuilder('\Magento\Sales\Model\Quote\Address\Rate')
            ->disableOriginalConstructor()
            ->setMethods(['getPrice', '__wakeup'])
            ->getMock();
        $shippingRateMock->expects($this->once())
            ->method('getPrice')
            ->will($this->returnValue($shippingPrice));

        $this->store->expects($this->once())
            ->method('convertPrice')
            ->with($shippingPrice, true, true)
            ->will($this->returnValue($convertedPrice));

        $this->priceObj->setShippingRate($shippingRateMock);
        $this->assertEquals($convertedPrice, $this->priceObj->getShippingPrice());
    }
}
