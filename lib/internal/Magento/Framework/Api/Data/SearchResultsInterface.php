<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api\Data;

/**
 * Search results interface.
 */
interface SearchResultsInterface
{
    /**
     * Get items list.
     *
     * @return \Magento\Framework\Api\Data\ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\Data\SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();
}
