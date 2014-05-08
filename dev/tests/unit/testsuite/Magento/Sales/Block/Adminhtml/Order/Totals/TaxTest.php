<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  integration_tests
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
     * Test class for getFullTaxInfo
     *
     * @param $source
     * @param $getCalculatedTax
     * @param $getShippingTax
     * @param $expectedResult
     *
     * @dataProvider getFullTaxInfoDataProvider
     */
    public function testGetFullTaxInfo($source, $getCalculatedTax, $getShippingTax, $expectedResult)
    {
        $taxHelperMock = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->setMethods(array('getCalculatedTaxes', 'getShippingTax'))
            ->disableOriginalConstructor()
            ->getMock();
        $taxHelperMock->expects($this->any())
            ->method('getCalculatedTaxes')
            ->will($this->returnValue($getCalculatedTax));
        $taxHelperMock->expects($this->any())
            ->method('getShippingTax')
            ->will($this->returnValue($getShippingTax));

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
     * 1st Case : $source is not an instance of Mage_Sales_Model_Order
     * 2nd Case : getCalculatedTaxes and getShippingTax return value
     *
     * @return array
     */
    public function getFullTaxInfoDataProvider()
    {
        $firstMock = $this->getMock('stdClass');

        $secondMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $getCalculatedTax = array(
            'tax' => 'tax',
            'shipping_tax' => 'shipping_tax'
        );
        $getShippingTax = array(
            'shipping_tax' => 'shipping_tax',
            'shipping_and_handing' => 'shipping_and_handing'
        );

        return array(
            array($firstMock, $getCalculatedTax, $getShippingTax, array()),
            array($secondMock, $getCalculatedTax, $getShippingTax, array('tax' => 'tax',
                'shipping_tax' => 'shipping_tax', 'shipping_and_handing' => 'shipping_and_handing'))
        );
    }
}
