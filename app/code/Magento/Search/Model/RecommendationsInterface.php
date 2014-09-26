<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

interface RecommendationsInterface
{
    /**
     * @param string $searchQueryText
     * @param int $limit
     * @param array $additionalFilters
     * @return \Magento\Search\Model\QueryResult[]
     */
    public function getRecommendations($searchQueryText, $limit = null, $additionalFilters = array());

    /**
     * @return bool
     */
    public function isCountResultsEnabled();
}
