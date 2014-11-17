<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Api\SearchResults;

/**
 * TaxRateSearchResults Service Data Object used for the search service requests
 */
class TaxRateSearchResults extends SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Service\V1\Data\TaxRate[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
