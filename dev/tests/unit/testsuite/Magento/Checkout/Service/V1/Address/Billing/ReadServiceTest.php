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
    protected $quoteRepositoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $converterMock;

    protected function setUp()
    {
        $this->quoteRepositoryMock = $this->getMock('\Magento\Sales\Model\QuoteRepository', [], [], '', false);
        $this->converterMock = $this->getMock('\Magento\Checkout\Service\V1\Address\Converter', [], [], '', false);

        $this->service = new ReadService($this->quoteRepositoryMock, $this->converterMock);
    }

    public  function testGetAddress()
    {
        $quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);
        $this->quoteRepositoryMock->expects($this->once())->method('get')
            ->with('cartId')->will($this->returnValue($quoteMock));

        $addressMock = $this->getMock('\Magento\Sales\Model\Quote\Address', [], [], '', false);
        $quoteMock->expects($this->any())->method('getBillingAddress')->will($this->returnValue($addressMock));

        $this->converterMock->expects($this->once())->method('convertModelToDataObject')
            ->with($addressMock)->will($this->returnValue('BillingAddress'));

        $this->assertEquals('BillingAddress', $this->service->getAddress('cartId'));
    }
}
