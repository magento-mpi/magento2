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
    /**
     * Test method for getFullTaxInfo
     *
     * @param \Magento\Sales\Model\Order $source
     * @param array $getCalculatedTax
     * @param array $expectedResult
     *
     * @dataProvider getFullTaxInfoDataProvider
     */
    public function testGetFullTaxInfo($source, $getCalculatedTax, $expectedResult)
    {
        $taxHelperMock = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->setMethods(array('getCalculatedTaxes'))
            ->disableOriginalConstructor()
            ->getMock();
        $taxHelperMock->expects($this->any())
            ->method('getCalculatedTaxes')
            ->will($this->returnValue($getCalculatedTax));

        $mockObject = $this->getMockBuilder('Magento\Sales\Block\Adminhtml\Order\Totals\Tax')
            ->setConstructorArgs($this->_getConstructArguments($taxHelperMock))
            ->setMethods(array('getOrder'))
            ->getMock();
        $mockObject->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($source));

        $actualResult = $mockObject->getFullTaxInfo();
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
            array('taxHelper' => $taxHelperMock)
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

        $getCalculatedTax = array(
            'tax' => 'tax',
            'shipping_tax' => 'shipping_tax'
        );

        return array(
            'source is not an instance of \Magento\Sales\Model\Order' =>
                array($notAnInstanceOfASalesModelOrder, $getCalculatedTax, array()),
            'source is an instance of \Magento\Sales\Model\Order and has reasonable data' =>
                array($salesModelOrderMock, $getCalculatedTax, array('tax' => 'tax',
                'shipping_tax' => 'shipping_tax'))
        );
    }
}
