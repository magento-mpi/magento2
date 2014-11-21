<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data;

/**
 * Class SearchResultInterface
 */
interface SearchResultInterface
{
    /**
     * Retrieve collection items
     *
     * @return \Magento\Framework\Object[]
     */
    public function getItems();

    /**
     * Retrieve count of currently loaded items
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * @return \Magento\Framework\Api\CriteriaInterface
     */
    public function getSearchCriteria();
}
