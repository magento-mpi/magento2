<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\InvoiceRepository;

/**
 * Class InvoiceVoid
 */
class InvoiceVoid
{
    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @param InvoiceRepository $invoiceRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Invoke InvoiceVoid service
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return (bool)$this->invoiceRepository->get($id)->void();
    }
}
