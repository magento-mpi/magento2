<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

interface AdditionalInfoDataProviderInterface
{
    /**
     * @param string $searchQueryText
     * @param int $limit
     * @param array $additionalFilters
     * @return \Magento\Search\Model\QueryResult[]
     */
    public function getSearchResult($searchQueryText, $limit = null, $additionalFilters = array());

    /**
     * @return bool
     */
    public function isCountResultsEnabled();
}
