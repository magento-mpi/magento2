<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Billing;

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
    protected $converterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    protected function setUp()
    {
        $this->quoteLoaderMock = $this->getMock('\Magento\Checkout\Service\V1\QuoteLoader', [], [], '', false);
        $this->storeManagerMock = $this->getMock('\Magento\Framework\StoreManagerInterface');
        $this->converterMock = $this->getMock('\Magento\Checkout\Service\V1\Address\Converter', [], [], '', false);

        $this->service = new ReadService($this->quoteLoaderMock, $this->converterMock, $this->storeManagerMock);
    }

    public  function testGetAddress()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $storeMock->expects($this->once())->method('getId')->will($this->returnValue(11));
        $this->storeManagerMock->expects($this->once())->method('getStore')->will($this->returnValue($storeMock));

        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteLoaderMock->expects($this->once())->method('load')
            ->with('cartId', '11')->will($this->returnValue($quoteMock));

        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);
        $quoteMock->expects($this->any())->method('getBillingAddress')->will($this->returnValue($addressMock));

        $this->converterMock->expects($this->once())->method('convertModelToDataObject')
            ->with($addressMock)->will($this->returnValue('BillingAddress'));

        $this->assertEquals('BillingAddress', $this->service->getAddress('cartId'));
    }
}
