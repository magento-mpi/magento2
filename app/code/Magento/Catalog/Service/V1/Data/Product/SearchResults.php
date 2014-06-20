<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\Product;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class SearchResults extends \Magento\Framework\Service\V1\Data\SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Catalog\Service\V1\Data\Product[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
