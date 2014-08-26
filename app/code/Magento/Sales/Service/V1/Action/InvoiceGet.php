<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Sales\Service\V1\Data\InvoiceMapper;

/**
 * Class InvoiceGet
 */
class InvoiceGet
{
    /**
     * @var InvoiceRepository
     */
    protected $invoiceRepository;

    /**
     * @var InvoiceMapper
     */
    protected $invoiceMapper;

    /**
     * @param InvoiceRepository $invoiceRepository
     * @param InvoiceMapper $invoiceMapper
     */
    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceMapper $invoiceMapper
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceMapper = $invoiceMapper;
    }

    /**
     * Invoke getInvoice service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Invoice
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function invoke($id)
    {
        return $this->invoiceMapper->extractDto($this->invoiceRepository->get($id));
    }
}
