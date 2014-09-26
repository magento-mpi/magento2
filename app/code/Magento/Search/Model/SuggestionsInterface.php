<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

interface SuggestionsInterface
{
    /**
     * Retrieve search suggestions
     *
     * @param string $query
     * @param int $limit
     * @param array $additionalFilters
     * @return \Magento\Search\Model\QueryResult[]
     */
    public function getSuggestions($query, $limit = null, $additionalFilters = array());

    /**
     * Retrieve search suggestions count results enabled
     *
     * @return bool
     */
    public function isCountResultsEnabled();
}
