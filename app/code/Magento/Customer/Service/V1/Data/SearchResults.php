<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;


use Magento\Customer\Service\V1\Data\SearchCriteria;

class SearchResults extends \Magento\Service\Entity\AbstractObject
{
    /**
     * @return array
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? [] : $this->_get('items');
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
