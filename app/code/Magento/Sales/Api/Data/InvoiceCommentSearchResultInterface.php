<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Api\Data;

/**
 * Interface InvoiceCommentSearchResultInterface
 */
interface InvoiceCommentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\InvoiceCommentInterface[]
     */
    public function getItems();
}
