<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Sales\Block\Adminhtml\Order\Totals\TaxTest
 */
namespace Magento\Sales\Block\Adminhtml\Order\Totals;

class TaxTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Block\Adminhtml\Order\Totals\Tax */
    private $taxMock;

    public function setUp()
    {
        $getCalculatedTax = [
            'tax' => 'tax',
            'shipping_tax' => 'shipping_tax'
        ];
        $getShippingTax = [
            'shipping_tax' => 'shipping_tax',
            'shipping_and_handing' => 'shipping_and_handing'
        ];
        $taxHelperMock = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->setMethods(['getCalculatedTaxes', 'getShippingTax'])
            ->disableOriginalConstructor()
            ->getMock();
        $taxHelperMock->expects($this->any())
            ->method('getCalculatedTaxes')
            ->will($this->returnValue($getCalculatedTax));
        $taxHelperMock->expects($this->any())
            ->method('getShippingTax')
            ->will($this->returnValue($getShippingTax));

        $this->taxMock = $this->getMockBuilder('Magento\Sales\Block\Adminhtml\Order\Totals\Tax')
            ->setConstructorArgs($this->_getConstructArguments($taxHelperMock))
            ->setMethods(['getOrder', 'getSource'])
            ->getMock();

    }

    /**
     * Test method for getFullTaxInfo
     *
     * @param \Magento\Sales\Model\Order $source
     * @param array $expectedResult
     *
     * @dataProvider getFullTaxInfoDataProvider
     */
    public function testGetFullTaxInfo($source, $expectedResult)
    {
        $this->taxMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($source));

        $actualResult = $this->taxMock->getFullTaxInfo();
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Test method for getFullTaxInfo with invoice or creditmemo
     *
     * @param \Magento\Sales\Model\Order\Invoice|\Magento\Sales\Model\Order\Creditmemo $source
     * @param array $expectedResult
     *
     * @dataProvider getCreditAndInvoiceFullTaxInfoDataProvider
     */
    public function testGetFullTaxInfoWithCreditAndInvoice(
        $source,
        $expectedResult
    ) {
        $this->taxMock->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue($source));

        $actualResult = $this->taxMock->getFullTaxInfo();
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Provide the tax helper mock as a constructor argument
     *
     * @param $taxHelperMock
     * @return array
     */
    protected function _getConstructArguments($taxHelperMock)
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        return $objectManagerHelper->getConstructArguments(
            'Magento\Sales\Block\Adminhtml\Order\Totals\Tax',
            ['taxHelper' => $taxHelperMock]
        );
    }

    /**
     * Data provider.
     * 1st Case : $source is not an instance of \Magento\Sales\Model\Order
     * 2nd Case : getCalculatedTaxes and getShippingTax return value
     *
     * @return array
     */
    public function getFullTaxInfoDataProvider()
    {
        $salesModelOrderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        return [
            'source is not an instance of \Magento\Sales\Model\Order' =>
                [null, []],
            'source is an instance of \Magento\Sales\Model\Order and has reasonable data' =>
                [
                    $salesModelOrderMock,
                    [
                        'tax' => 'tax',
                        'shipping_tax' => 'shipping_tax',
                        'shipping_and_handing' => 'shipping_and_handing'
                    ]
                ]
        ];
    }

    /**
     * Data provider.
     * 1st Case : $current an instance of \Magento\Sales\Model\Invoice
     * 2nd Case : $current an instance of \Magento\Sales\Model\Creditmemo
     *
     * @return array
     */
    public function getCreditAndInvoiceFullTaxInfoDataProvider()
    {
        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();
        $creditMemoMock = $this->getMockBuilder('Magento\Sales\Model\Order\Creditmemo')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        $expected = [
            'tax' => 'tax',
            'shipping_tax' => 'shipping_tax',
            'shipping_and_handing' => 'shipping_and_handing'
        ];
        return [
            'invoice' =>
                [$invoiceMock, $expected],
            'creditMemo' =>
                [$creditMemoMock, $expected]
        ];
    }
}
