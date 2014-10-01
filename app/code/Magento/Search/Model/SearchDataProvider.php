<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

class SearchDataProvider implements SearchDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSearchData(QueryInterface $query, $limit = null, $additionalFilters = array())
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isCountResultsEnabled()
    {
        return false;
    }
}
