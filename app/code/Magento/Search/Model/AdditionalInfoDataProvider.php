<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

class AdditionalInfoDataProvider implements AdditionalInfoDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSearchResult($searchQueryText, $limit = null, $additionalFilters = array())
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
