<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Api;

/**
 * Invoice comment repository interface.
 *
 * An invoice is a record of the receipt of payment for an order. An invoice can include comments that detail the
 * invoice history.
 */
interface InvoiceCommentRepositoryInterface
{
    /**
     * Lists invoice comments that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $criteria The search criteria.
     * @return \Magento\Sales\Api\Data\InvoiceSearchResultInterface Invoice search result interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $criteria);

    /**
     * Loads a specified invoice comment.
     *
     * @param int $id The invoice comment ID.
     * @return \Magento\Sales\Api\Data\InvoiceCommentInterface Invoice comment interface.
     */
    public function get($id);

    /**
     * Deletes a specified invoice comment.
     *
     * @param \Magento\Sales\Api\Data\InvoiceCommentInterface $entity The invoice comment.
     * @return bool
     */
    public function delete(\Magento\Sales\Api\Data\InvoiceCommentInterface $entity);

    /**
     * Performs persist operations for a specified invoice comment.
     *
     * @param \Magento\Sales\Api\Data\InvoiceCommentInterface $entity The invoice comment.
     * @return \Magento\Sales\Api\Data\InvoiceCommentInterface Invoice comment interface.
     */
    public function save(\Magento\Sales\Api\Data\InvoiceCommentInterface $entity);
}
