<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

class SearchResults extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return array
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? [] : $this->_get('items');
    }

    /**
     * @return \Magento\Customer\Service\V1\Dto\SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get('search_criteria');
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get('total_count');
    }
}
