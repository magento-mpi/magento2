<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface InvoiceCreateInterface
 * @package Magento\Sales\Service\V1
 */
interface InvoiceCreateInterface
{
    /**
     * @param \Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject
     * @return bool
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Invoice $invoiceDataObject);
}
