<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface InvoiceVoidInterface
 */
interface InvoiceVoidInterface
{
    /**
     * Invoke InvoiceVoid service
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id);
}
