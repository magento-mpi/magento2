<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class SearchResults extends \Magento\Framework\Service\Data\AbstractExtensibleObject implements SearchResultsInterface
{
    const KEY_ITEMS = 'items';
    const KEY_SEARCH_CRITERIA = 'search_criteria';
    const KEY_TOTAL_COUNT = 'total_count';

    /**
     * Get items
     *
     * @return \Magento\Framework\Service\Data\AbstractExtensibleObject[]
     */
    public function getItems()
    {
        return is_null($this->_get(self::KEY_ITEMS)) ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get(self::KEY_SEARCH_CRITERIA);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }
}
