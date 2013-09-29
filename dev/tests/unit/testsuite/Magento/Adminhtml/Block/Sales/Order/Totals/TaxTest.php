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
     * @var Magento_Adminhtml_Block_Sales_Order_Totals_Tax
     */
    protected $_block;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Instantiate Magento_Adminhtml_Block_Sales_Order_Totals_Tax block
     */
    protected function setUp()
    {
        $this->_block = $this->getMockBuilder('Magento_Adminhtml_Block_Sales_Order_Totals_Tax')
            ->setConstructorArgs($this->_getModelArgument())
            ->setMethods(array('getOrder'))
            ->getMock();
    }

    /**
     * Module arguments for Magento_Adminhtml_Block_Sales_Order_Totals_Tax
     *
     * @return array
     */
    protected function _getModelArgument()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $attributeFactory = $this->getMock('Magento_Eav_Model_Entity_AttributeFactory',
            array('create'), array(), '', false);
        $taxItemFactory = $this->getMock('Magento_Tax_Model_Resource_Sales_Order_Tax_ItemFactory',
            array('create'), array(), '', false);
        $taxHelperMock = $objectManagerHelper->getObject('Magento_Tax_Helper_Data', array(
            'attributeFactory' => $attributeFactory,
            'taxItemFactory' => $taxItemFactory
        ));

        $taxOrderFactory = $this->getMock('Magento_Tax_Model_Sales_Order_Tax_Factory',
            array('create'), array(), '', false);

        return $objectManagerHelper->getConstructArguments(
            'Magento_Adminhtml_Block_Sales_Order_Totals_Tax',
            array(
                'taxHelper'       => $taxHelperMock,
                'taxOrderFactory' => $taxOrderFactory,
            )
        );
    }

    /**
     * @return Magento_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getSalesOrderMock()
    {
        $orderMock = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->setMethods(array('getItemsCollection'))
            ->disableOriginalConstructor()
            ->getMock();
        $orderMock->expects($this->any())
            ->method('getItemsCollection')
            ->will($this->returnValue(array()));
        return $orderMock;
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
