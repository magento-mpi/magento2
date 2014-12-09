<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Api\Data;

/**
 * Interface InvoiceSearchResultInterface
 */
interface InvoiceSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\InvoiceInterface[]
     */
    public function getItems();
}
