<?php
/**
{license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class SearchResults
 */
class SearchResults extends \Magento\Service\Entity\AbstractDto
{
    /**
     * Get items
     *
     * @return \Magento\Service\Entity\AbstractDto[]
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? [] : $this->_get('items');
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Customer\Service\V1\Dto\SearchCriteria
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
