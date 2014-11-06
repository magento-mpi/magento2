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
 * Data object for Tax class search results.
 */
class TaxClassSearchResults extends SearchResults
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Service\V1\Data\TaxClass[]
     */
    public function getItems()
    {
        return parent::getItems();
    }
}
