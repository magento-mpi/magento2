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


use Magento\Customer\Service\V1\Dto\SearchCriteria;

class SearchResults extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_get('items');
    }

    /**
     * @return SearchCriteria
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
