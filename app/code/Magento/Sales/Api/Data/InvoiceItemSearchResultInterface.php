<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Api\Data;

/**
 * Interface InvoiceItemSearchResultInterface
 */
interface InvoiceItemSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\InvoiceItemInterface[]
     */
    public function getItems();
}
