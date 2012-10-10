<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Order_Print_InvoiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetInvoiceTotalsHtml()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $order = Mage::getModel('Mage_Sales_Model_Order');
        Mage::register('current_order', $order);
        $payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
        $payment->setMethod('checkmo');
        $order->setPayment($payment);

        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = Mage::app()->getLayout()->createBlock('Mage_Sales_Block_Order_Print_Invoice');
        $layout->addBlock($block, 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'invoice_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $invoice = Mage::getModel('Mage_Sales_Model_Order_Invoice');
        $this->assertEmpty($childBlock->getInvoice());
        $this->assertNotEquals($expectedHtml, $block->getInvoiceTotalsHtml($invoice));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getInvoiceTotalsHtml($invoice);
        $this->assertSame($invoice, $childBlock->getInvoice());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
