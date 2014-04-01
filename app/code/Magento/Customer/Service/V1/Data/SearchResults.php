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
class SearchResults extends \Magento\Service\Data\AbstractObject
{
    /**
     * Get items
     *
     * @return \Magento\Service\Data\AbstractObject[]
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? array() : $this->_get('items');
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Customer\Service\V1\Data\SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get('search_criteria');
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get('total_count');
    }
}
