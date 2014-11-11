<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Data;

/**
 * Low stock search result data object
 *
 * @codeCoverageIgnore
 */
interface SearchResultInterface
{
    /**#@+
     * Search result object data keys
     */
    const PRODUCT_SKU_LIST = 'items';
    const SEARCH_CRITERIA = 'search_criteria';
    const TOTAL_COUNT = 'total_count';
    /**#@-*/

    /**
     * Get items
     *
     * @return string[]
     */
    public function getItems();

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Api\SearchCriteria
     */
    public function getSearchCriteria();

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount();
}
