<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Order_Invoice_Total_ShippingTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieve new invoice collection from an array of invoices' data
     *
     * @param array $invoicesData
     * @return Magento_Data_Collection
     */
    protected function _getInvoiceCollection(array $invoicesData)
    {
        $className = 'Magento_Sales_Model_Order_Invoice';
        $result = new Magento_Data_Collection();
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'orderFactory' => $this->getMock(
                'Magento_Sales_Model_OrderFactory', array(), array(), '', false
            ),
            'orderResourceFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_OrderFactory', array(), array(), '', false
            ),
            'calculatorFactory' => $this->getMock(
                'Magento_Core_Model_CalculatorFactory', array(), array(), '', false
            ),
            'invoiceItemCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Invoice_Item_CollectionFactory', array(), array(), '', false
            ),
            'invoiceCommentFactory' => $this->getMock(
                'Magento_Sales_Model_Order_Invoice_CommentFactory', array(), array(), '', false
            ),
            'commentCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Invoice_Comment_CollectionFactory', array(), array(), '', false
            ),
            'templateMailerFactory' => $this->getMock(
                'Magento_Core_Model_Email_Template_MailerFactory', array(), array(), '', false
            ),
            'emailInfoFactory' => $this->getMock(
                'Magento_Core_Model_Email_InfoFactory', array(), array(), '', false
            ),
        );
        foreach ($invoicesData as $oneInvoiceData) {
            $arguments['data'] = $oneInvoiceData;
            $arguments = $objectManagerHelper->getConstructArguments($className, $arguments);
            /** @var $prevInvoice Magento_Sales_Model_Order_Invoice */
            $prevInvoice = $this->getMock($className, array('_init'), $arguments);
            $result->addItem($prevInvoice);
        }
        return $result;
    }

    /**
     * @dataProvider collectDataProvider
     * @param array $prevInvoicesData
     * @param float $orderShipping
     * @param float $invoiceShipping
     * @param float $expectedShipping
     */
    public function testCollect(array $prevInvoicesData, $orderShipping, $invoiceShipping, $expectedShipping)
    {
        $objectManager = new Magento_TestFramework_Helper_ObjectManager($this);
        $arguments = array(
            'productFactory' => $this->getMock(
                'Magento_Catalog_Model_ProductFactory', array(), array(), '', false
            ),
            'templateMailerFactory' => $this->getMock(
                'Magento_Core_Model_Email_Template_MailerFactory', array(), array(), '', false
            ),
            'emailInfoFactory' => $this->getMock(
                'Magento_Core_Model_Email_InfoFactory', array(), array(), '', false
            ),
            'orderItemCollFactory' => $this->getMock(
                'Magento_Sales_Model_Resource_Order_Item_CollectionFactory', array(), array(), '', false
            ),
            'serviceOrderFactory' => $this->getMock(
                'Magento_Sales_Model_Service_OrderFactory', array(), array(), '', false
            ),
            'currencyFactory' => $this->getMock(
                'Magento_Directory_Model_CurrencyFactory', array(), array(), '', false
            ),
            'orderHistoryFactory' => $this->getMock(
                'Magento_Sales_Model_Order_Status_HistoryFactory', array(), array(), '', false
            ),
            'orderTaxCollFactory' => $this->getMock(
                'Magento_Tax_Model_Resource_Sales_Order_Tax_CollectionFactory', array(), array(), '', false
            ),
        );
        $orderConstructorArgs = $objectManager->getConstructArguments('Magento_Sales_Model_Order', $arguments);
        /** @var $order Magento_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject */
        $order = $this->getMock('Magento_Sales_Model_Order', array('_init', 'getInvoiceCollection'),
            $orderConstructorArgs, '', false);
        $order->setData('shipping_amount', $orderShipping);
        $order->expects($this->any())
            ->method('getInvoiceCollection')
            ->will($this->returnValue($this->_getInvoiceCollection($prevInvoicesData)));
        /** @var $invoice Magento_Sales_Model_Order_Invoice|PHPUnit_Framework_MockObject_MockObject */
        $invoice = $this->getMock('Magento_Sales_Model_Order_Invoice', array('_init'), array(), '', false);
        $invoice->setData('shipping_amount', $invoiceShipping);
        $invoice->setOrder($order);

        $total = new Magento_Sales_Model_Order_Invoice_Total_Shipping();
        $total->collect($invoice);

        $this->assertEquals($expectedShipping, $invoice->getShippingAmount());
    }

    public static function collectDataProvider()
    {
        return array(
            'no previous invoices' => array(
                'prevInvoicesData' => array(array()),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '0.0000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 10.00
            ),
            'non-zero shipping in previous invoices' => array(
                'prevInvoicesData' => array(array('shipping_amount' => '10.000')),
                'orderShipping'    => 10.00,
                'invoiceShipping'  => 5.00,
                'expectedShipping' => 0
            ),
        );
    }
}
