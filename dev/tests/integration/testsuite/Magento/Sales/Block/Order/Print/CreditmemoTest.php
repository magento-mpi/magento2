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

class Magento_Sales_Block_Order_Print_CreditmemoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetTotalsHtml()
    {
        $order = Mage::getModel('Magento\Sales\Model\Order');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_order', $order);
        $payment = Mage::getModel('Magento\Sales\Model\Order\Payment');
        $payment->setMethod('checkmo');
        $order->setPayment($payment);

        $layout = Mage::getSingleton('Magento\Core\Model\Layout');
        $block = $layout->createBlock('Magento\Sales\Block\Order\PrintOrder\Creditmemo', 'block');
        $childBlock = $layout->addBlock('Magento\Core\Block\Text', 'creditmemo_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $creditmemo = Mage::getModel('Magento\Sales\Model\Order\Creditmemo');
        $this->assertEmpty($childBlock->getCreditmemo());
        $this->assertNotEquals($expectedHtml, $block->getTotalsHtml($creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getTotalsHtml($creditmemo);
        $this->assertSame($creditmemo, $childBlock->getCreditmemo());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
