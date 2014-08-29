<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\InvoiceGet;
use Magento\Sales\Service\V1\Action\InvoiceList;
use Magento\Sales\Service\V1\Action\InvoiceCommentsList;
use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Class InvoiceRead
 */
class InvoiceRead implements InvoiceReadInterface
{
    /**
     * @var InvoiceGet
     */
    protected $invoiceGet;

    /**
     * @var InvoiceList
     */
    protected $invoiceList;

    /**
     * @var InvoiceCommentsList
     */
    protected $invoiceCommentsList;

    /**
     * @var InvoiceGetStatus
     */
    protected $invoiceGetStatus;

    /**
     * @param InvoiceGet $invoiceGet
     * @param InvoiceList $invoiceList
     * @param InvoiceCommentsList $invoiceCommentsList
     */
    public function __construct(
        InvoiceGet $invoiceGet,
        InvoiceList $invoiceList,
        InvoiceCommentsList $invoiceCommentsList
    ) {
        $this->invoiceGet = $invoiceGet;
        $this->invoiceList = $invoiceList;
        $this->invoiceCommentsList = $invoiceCommentsList;
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\Invoice
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        return $this->invoiceGet->invoke($id);
    }

    /**
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function search(SearchCriteria $searchCriteria)
    {
        return $this->invoiceList->invoke($searchCriteria);
    }

    /**
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function commentsList($id)
    {
        return $this->invoiceCommentsList->invoke($id);
    }
}
