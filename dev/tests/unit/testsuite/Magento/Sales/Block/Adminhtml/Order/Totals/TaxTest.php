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
            ->setMethods(['getOrder'])
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
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Test method for getFullTaxInfo with argument
     *
     * @param \Magento\Sales\Model\Order\Invoice $source
     * @param \Magento\Sales\Model\Order\Invoice $current
     * @param array $expectedResult
     *
     * @dataProvider getCreditAndInvoiceFullTaxInfoDataProvider
     */
    public function testGetFullTaxInfoWithCreditMemo(
        $source,
        $current,
        $expectedResult
    ) {
        $this->taxMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($source));

        $actualResult = $this->taxMock->getFullTaxInfo($current);
        $this->assertEquals($expectedResult, $actualResult);
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
        $notAnInstanceOfASalesModelOrder = $this->getMock('stdClass');

        $salesModelOrderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();
        return [
            'source is not an instance of \Magento\Sales\Model\Order' =>
                [$notAnInstanceOfASalesModelOrder, []],
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
     * 1st Case : $current is not an instance of \Magento\Sales\Model\Invoice
     * 2nd Case : $current is not an instance of \Magento\Sales\Model\Creditmemo
     *
     * @return array
     */
    public function getCreditAndInvoiceFullTaxInfoDataProvider()
    {
        $salesModelOrderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

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
                [$salesModelOrderMock, $invoiceMock, $expected],
            'creditMemo' =>
                [$salesModelOrderMock, $creditMemoMock, $expected]
        ];
    }
}
