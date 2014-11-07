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
class SearchResults extends \Magento\Framework\Api\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
