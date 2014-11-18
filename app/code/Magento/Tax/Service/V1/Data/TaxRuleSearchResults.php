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
 * TaxRuleSearchResults Service Data Object used for the search service requests
 * @deprecated
 * @see \Magento\Tax\Api\Data\TaxRuleSearchResultsInterface
 */
class TaxRuleSearchResults extends SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Service\V1\Data\TaxRule[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
