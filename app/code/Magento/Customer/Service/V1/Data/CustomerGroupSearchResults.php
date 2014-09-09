<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class CustomerGroupSearchResults extends \Magento\Framework\Service\V1\Data\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
