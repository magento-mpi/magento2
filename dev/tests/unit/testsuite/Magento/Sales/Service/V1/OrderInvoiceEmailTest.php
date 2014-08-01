<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

/**
 * Test Class OrderInvoiceEmailTest for Order Service
 *
 * @package Magento\Sales\Service\V1
 */
class OrderInvoiceEmailTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $invoiceId = 1;
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $invoiceRepository = $this->getMock('\Magento\Sales\Model\InvoiceRepository', ['get'], [], '', false);
        $notifier = $this->getMock('\Magento\Sales\Model\InvoiceNotifier', ['notify', '__wakeup'], [], '', false);
        $invoice = $this->getMock(
            '\Magento\Sales\Model\Invoice',
            ['__wakeup', 'getEmailSent'],
            [],
            '',
            false
        );

        $service = $objectManager->getObject(
            'Magento\Sales\Service\V1\OrderInvoiceEmail',
            [
                'invoiceRepository' => $invoiceRepository,
                'notifier' => $notifier
            ]
        );
        $invoiceRepository->expects($this->once())
            ->method('get')
            ->with($invoiceId)
            ->will($this->returnValue($invoice));
        $notifier->expects($this->any())
            ->method('notify')
            ->with($invoice)
            ->will($this->returnValue(true));
        $this->assertTrue($service->invoke($invoiceId));
    }
}
 