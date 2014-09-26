<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

class Suggestions implements SuggestionsInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSuggestions($query, $limit = null, $additionalFilters = array())
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
