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

class Mage_Sales_Block_Order_Print_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetTotalsHtml()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $order = Mage::getModel('Mage_Sales_Model_Order');
        Mage::register('current_order', $order);
        $payment = Mage::getModel('Mage_Sales_Model_Order_Payment');
        $payment->setMethod('checkmo');
        $order->setPayment($payment);

        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = Mage::app()->getLayout()->createBlock('Mage_Sales_Block_Order_Print_Creditmemo');
        $layout->addBlock($block, 'block');
        $childBlock = $layout->addBlock('Mage_Core_Block_Text', 'creditmemo_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo');
        $this->assertEmpty($childBlock->getCreditmemo());
        $this->assertNotEquals($expectedHtml, $block->getTotalsHtml($creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getTotalsHtml($creditmemo);
        $this->assertSame($creditmemo, $childBlock->getCreditmemo());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
