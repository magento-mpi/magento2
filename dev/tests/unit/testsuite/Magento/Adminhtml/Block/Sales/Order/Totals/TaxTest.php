<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Adminhtml_Block_Sales_Order_Totals_TaxTest
 */
class Magento_Adminhtml_Block_Sales_Order_Totals_TaxTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Adminhtml\Block\Sales\Order\Totals\Tax
     */
    protected $_block;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Instantiate \Magento\Adminhtml\Block\Sales\Order\Totals\Tax block
     */
    protected function setUp()
    {
        $this->_block = $this->getMockBuilder('Magento\Adminhtml\Block\Sales\Order\Totals\Tax')
            ->setConstructorArgs($this->_getModelArgument())
            ->setMethods(array('getOrder'))
            ->getMock();
    }

    /**
     * Module arguments for \Magento\Adminhtml\Block\Sales\Order\Totals\Tax
     *
     * @return array
     */
    protected function _getModelArgument()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        return $objectManagerHelper->getConstructArguments(
            'Magento\Adminhtml\Block\Sales\Order\Totals\Tax',
            array(
                'context'         => $this->getMock('Magento\Backend\Block\Template\Context', array(), array(), '',
                    false),
                'taxConfig'       => $this->getMock('Magento\Tax\Model\Config', array(), array(), '', false),
                'taxHelper'       => $this->_getTaxHelperMock(),
                'taxCalculation'  => $this->getMock('Magento\Tax\Model\Calculation', array(), array(), '', false),
                'taxOrderFactory' => $this->getMock('Magento\Tax\Model\Sales\Order\Tax\Factory', array(), array(), '',
                    false),
            )
        );
    }

    /**
     * @return \Magento\Sales\Model\Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getSalesOrderMock()
    {
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->setMethods(array('getItemsCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock->expects($this->any())
            ->method('getItemsCollection')
            ->will($this->returnValue(array()));
        return $orderMock;
    }

    /**
     * @return \Magento\Tax\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getTaxHelperMock()
    {
        $taxHelper = $this->getMockBuilder('Magento\Tax\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        return $taxHelper;
    }

    /**
     * Test MAGETWO-1653: Incorrect tax summary for partial credit memos/invoices
     *
     * @dataProvider getSampleData
     */
    public function testAddAttributesToForm($actual, $expected)
    {
        $orderMock = $this->_getSalesOrderMock();
        $orderMock->setData($actual);
        $this->_block->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));
        $fullTaxInfo = $this->_block->getFullTaxInfo();
        $this->assertEquals(reset($fullTaxInfo), $expected);
        $this->assertTrue(true);
    }

    /**
     * Data provider with sample data for tax order
     *
     * @return array
     */
    public function getSampleData()
    {
        return array(
            array(
                'actual'   => array(
                    'calculated_taxes'         => array(),
                    'shipping_tax'             => array(),
                    'shipping_tax_amount'      => 1.25,
                    'base_shipping_tax_amount' => 3.25,
                    'tax_amount'               => 0.16,
                    'base_tax_amount'          => 2
                ),
                'expected' => array(
                    'tax_amount'      => 1.25,
                    'base_tax_amount' => 3.25,
                    'title'           => 'Shipping & Handling Tax',
                    'percent'         => NULL,
                )
            )
        );
    }
}
