<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

/**
 * Test Class InvoiceEmailTest for Order Service
 *
 * @package Magento\Sales\Service\V1
 */
class InvoiceEmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceNotifier
     */
    protected $notifier;

    public function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->invoiceRepository = $this->getMock(
            '\Magento\Sales\Model\Order\InvoiceRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->notifier = $this->getMock(
            '\Magento\Sales\Model\Order\InvoiceNotifier',
            ['notify', '__wakeup'],
            [],
            '',
            false
        );

        $this->service = $objectManager->getObject(
            'Magento\Sales\Service\V1\InvoiceEmail',
            [
                'invoiceRepository' => $this->invoiceRepository,
                'notifier' => $this->notifier
            ]
        );
    }

    public function testInvoke()
    {
        $invoiceId = 1;
        $invoice = $this->getMock(
            '\Magento\Sales\Model\Order\Invoice',
            ['__wakeup', 'getEmailSent'],
            [],
            '',
            false
        );

        $this->invoiceRepository->expects($this->once())
            ->method('get')
            ->with($invoiceId)
            ->will($this->returnValue($invoice));
        $this->notifier->expects($this->any())
            ->method('notify')
            ->with($invoice)
            ->will($this->returnValue(true));

        $this->assertTrue($this->service->invoke($invoiceId));
    }
}
 