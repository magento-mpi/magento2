<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\InvoiceConverter;

/**
 * Class InvoiceCreate
 * @package Magento\Sales\Service\V1
 */
class InvoiceCreate
{
    /**
     * @var InvoiceConverter
     */
    protected $invoiceConverter;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $logger;

    /**
     * @param InvoiceConverter $invoiceConverter
     * @param \Magento\Framework\Logger $logger
     */
    public function __construct(InvoiceConverter $invoiceConverter, \Magento\Framework\Logger $logger)
    {
        $this->invoiceConverter = $invoiceConverter;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject
     * @return bool
     * @throws \Exception
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject)
    {
        try {
            /** @var \Magento\Sales\Model\Order\Invoice $invoice */
            $invoice = $this->invoiceConverter->getModel($invoiceDataObject);
            if (!$invoice) {
                return false;
            }
            $invoice->register();
            $invoice->save();
            return true;
        } catch (\Exception $e) {
            $this->logger->logException($e);
            throw new \Exception(__('An error has occurred during creating Invoice'));
        }
    }
}
