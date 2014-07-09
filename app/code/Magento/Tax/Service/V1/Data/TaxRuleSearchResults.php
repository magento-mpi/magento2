<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Customer\Service\V1\Data\SearchResults as CustomerServiceDataSearchResults;

/**
 * TaxRuleSearchResults Service Data Object used for the search service requests
 */
class TaxRuleSearchResults extends CustomerServiceDataSearchResults
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
