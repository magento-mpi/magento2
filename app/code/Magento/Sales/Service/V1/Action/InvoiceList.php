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
use Magento\Sales\Service\V1\Data\InvoiceSearchResultsBuilder;
use Magento\Framework\Data\SearchCriteria;

/**
 * Class InvoiceList
 */
class InvoiceList
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
     * @var InvoiceSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @param InvoiceRepository $invoiceRepository
     * @param InvoiceMapper $invoiceMapper
     * @param InvoiceSearchResultsBuilder $searchResultsBuilder
     */
    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceMapper $invoiceMapper,
        InvoiceSearchResultsBuilder $searchResultsBuilder
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceMapper = $invoiceMapper;
        $this->searchResultsBuilder = $searchResultsBuilder;
    }

    /**
     * Invoke InvoiceList service
     *
     * @param SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function invoke(SearchCriteria $searchCriteria)
    {
        $invoices = [];
        foreach ($this->invoiceRepository->find($searchCriteria) as $invoice) {
            $invoices[] = $this->invoiceMapper->extractDto($invoice);
        }
        return $this->searchResultsBuilder->setItems($invoices)
            ->setTotalCount(count($invoices))
            ->setSearchCriteria($searchCriteria)
            ->create();
    }
}
