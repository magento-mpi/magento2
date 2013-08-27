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
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        foreach ($invoicesData as $oneInvoiceData) {
            $arguments = $objectManagerHelper->getConstructArguments($className, array('data' => $oneInvoiceData));
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
        /** @var $order Magento_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject */
        $order = $this->getMock('Magento_Sales_Model_Order', array('_init', 'getInvoiceCollection'), array(), '',
            false);
        $order->setData('shipping_amount', $orderShipping);
        $order->expects($this->any())
            ->method('getInvoiceCollection')
            ->will($this->returnValue($this->_getInvoiceCollection($prevInvoicesData)))
        ;
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
