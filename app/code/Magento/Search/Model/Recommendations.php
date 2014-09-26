<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

class Recommendations implements RecommendationsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRecommendations($searchQueryText, $limit = null, $additionalFilters = array())
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
