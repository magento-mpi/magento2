<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

interface SearchDataProviderInterface
{
    /**
     * @param QueryInterface $query
     * @param int $limit
     * @param array $additionalFilters
     * @return \Magento\Search\Model\QueryResult[]
     */
    public function getSearchData(QueryInterface $query, $limit = null, $additionalFilters = array());

    /**
     * @return bool
     */
    public function isCountResultsEnabled();
}
