<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\InvoiceConverter;

class InvoiceCreate implements InvoiceCreateInterface
{
    /**
     * @var InvoiceConverter
     */
    protected $invoiceConverter;

    /**
     * @param InvoiceConverter $invoiceConverter
     */
    public function __construct(InvoiceConverter $invoiceConverter)
    {
        $this->invoiceConverter = $invoiceConverter;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject
     * @return bool
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject)
    {
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        $invoice = $this->invoiceConverter->getModel($invoiceDataObject);
        $invoice->register();
        return (bool)$invoice->save();
    }
}
