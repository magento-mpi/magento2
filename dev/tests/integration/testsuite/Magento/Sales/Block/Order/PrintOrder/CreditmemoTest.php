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

namespace Magento\Sales\Block\Order\PrintOrder;

class CreditmemoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetTotalsHtml()
    {
        $order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order');
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('current_order', $order);
        $payment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Payment');
        $payment->setMethod('checkmo');
        $order->setPayment($payment);

        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $block = $layout->createBlock('Magento\Sales\Block\Order\PrintOrder\Creditmemo', 'block');
        $childBlock = $layout->addBlock('Magento\Core\Block\Text', 'creditmemo_totals', 'block');

        $expectedHtml = '<b>Any html</b>';
        $creditmemo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Order\Creditmemo');
        $this->assertEmpty($childBlock->getCreditmemo());
        $this->assertNotEquals($expectedHtml, $block->getTotalsHtml($creditmemo));

        $childBlock->setText($expectedHtml);
        $actualHtml = $block->getTotalsHtml($creditmemo);
        $this->assertSame($creditmemo, $childBlock->getCreditmemo());
        $this->assertEquals($expectedHtml, $actualHtml);
    }
}
