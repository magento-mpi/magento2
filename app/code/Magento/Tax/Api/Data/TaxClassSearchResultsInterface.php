<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * Data object for Tax class search results.
 * @see \Magento\Tax\Service\V1\Data\TaxClassSearchResults
 */
interface TaxClassSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magento\Tax\Api\Data\TaxClassInterface[]
     */
    public function getItems();
}