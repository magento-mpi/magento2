<?php
/** 
 * 
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\Checkout\Service\V1\Data\Cart\ShippingMethod;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $methodBuilderMock;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $this->methodBuilderMock =
            $this->getMock('\Magento\Checkout\Service\V1\Data\Cart\ShippingMethodBuilder', [], [], '', false);
        $this->storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteAddressMock = $this->getMock(
            '\Magento\Sales\Model\Quote\Address',
            ['getShippingMethod', 'getShippingDescription', 'getShippingAmount', 'getBaseShippingAmount', '__wakeup'],
            [],
            '',
            false
        );

        $this->service = new ReadService($this->quoteLoaderMock, $this->storeManagerMock, $this->methodBuilderMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Cart contains virtual product(s) only. Shipping method is not applicable
     */
    public function testGetMethodWithVirtualProductException()
    {
        $storeId = 12;
        $cartId = 666;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('isVirtual')->will($this->returnValue(true));
        $this->quoteMock->expects($this->never())->method('getShippingAddress');

        $this->service->getMethod($cartId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage Shipping method and carrier are not set for the quote
     */
    public function testGetMethodWhenShippingMethodAndCarrierAreNotSet()
    {
        $storeId = 12;
        $cartId = 666;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('isVirtual')->will($this->returnValue(false));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())->method('getShippingMethod')->will($this->returnValue(false));

        $this->service->getMethod($cartId);
    }

    /**
     * @dataProvider getMethodDataProvider
     */
    public function testGetMethod($shippingDescription)
    {
        $storeId = 12;
        $cartId = 666;

        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($this->storeMock));
        $this->storeMock->expects($this->once())->method('getId')->will($this->returnValue($storeId));
        $this->quoteLoaderMock->expects($this->once())
            ->method('load')->with($cartId, $storeId)->will($this->returnValue($this->quoteMock));
        $this->quoteMock->expects($this->once())->method('isVirtual')->will($this->returnValue(false));
        $this->quoteMock->expects($this->once())
            ->method('getShippingAddress')->will($this->returnValue($this->quoteAddressMock));
        $this->quoteAddressMock->expects($this->once())
            ->method('getShippingMethod')->will($this->returnValue('one_two'));
        $this->quoteAddressMock->expects($this->once())
            ->method('getShippingDescription')->will($this->returnValue($shippingDescription));
        $this->quoteAddressMock->expects($this->once())->method('getShippingAmount')->will($this->returnValue(123.56));
        $this->quoteAddressMock->expects($this->once())
            ->method('getBaseShippingAmount')->will($this->returnValue(100.06));
        $output = [
            ShippingMethod::CARRIER_CODE => 'one',
            ShippingMethod::METHOD_CODE => 'two',
            ShippingMethod::DESCRIPTION => $shippingDescription,
            ShippingMethod::SHIPPING_AMOUNT => 123.56,
            ShippingMethod::BASE_SHIPPING_AMOUNT => 100.06,
        ];
        $this->methodBuilderMock->expects($this->once())
            ->method('populateWithArray')->with($output)->will($this->returnValue($this->methodBuilderMock));
        $this->methodBuilderMock->expects($this->once())->method('create');

        $this->service->getMethod($cartId);
    }

    public function getMethodDataProvider()
    {
        return array(
          array(''),
          array('some shipping description'),
        );
    }
}
 