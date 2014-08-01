<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Model\InvoiceRepository;

/**
 * Class OrderInvoiceEmail
 */
class OrderInvoiceEmail implements OrderInvoiceEmailInterface
{
    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var \Magento\Sales\Model\InvoiceNotifier
     */
    protected $invoiceNotifier;

    /**
     * @param InvoiceRepository $invoiceRepository
     * @param \Magento\Sales\Model\InvoiceNotifier $notifier
     */
    public function __construct(
        InvoiceRepository $invoiceRepository,
        \Magento\Sales\Model\InvoiceNotifier $notifier
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceNotifier = $notifier;
    }

    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id)
    {
        /** @var \Magento\Sales\Model\Order $invoice */
        $invoice = $this->invoiceRepository->get($id);
        return $this->invoiceNotifier->notify($invoice);
    }
}
