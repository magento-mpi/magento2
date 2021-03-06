<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\DataProvider;

use Magento\Search\Model\QueryInterface;
use Magento\Search\Model\SearchDataProviderInterface;

class Suggestions implements SearchDataProviderInterface
{
    const CONFIG_SUGGESTION_COUNT_RESULTS_ENABLED = 'server_suggestion_count_results_enabled';
    const CONFIG_SUGGESTION_ENABLED = 'server_suggestion_enabled';

    /**
     * @var \Magento\Solr\Model\Suggestions
     */
    private $suggestions;

    /**
     * @var \Magento\Solr\Helper\Data
     */
    private $searchData;

    /**
     * @var \Magento\Search\Model\QueryResultFactory
     */
    private $queryResultFactory;

    /**
     * @param \Magento\Solr\Model\Suggestions $suggestions
     * @param \Magento\Solr\Helper\Data $searchData
     * @param \Magento\Search\Model\QueryResultFactory $queryResultFactory
     */
    public function __construct(
        \Magento\Solr\Model\Suggestions $suggestions,
        \Magento\Solr\Helper\Data $searchData,
        \Magento\Search\Model\QueryResultFactory $queryResultFactory
    ) {
        $this->suggestions = $suggestions;
        $this->searchData = $searchData;
        $this->queryResultFactory = $queryResultFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSearchData(QueryInterface $query, $limit = null, $additionalFilters = null)
    {
        $isSearchSuggestionsEnabled = (bool)$this->searchData->getSolrConfigData(self::CONFIG_SUGGESTION_ENABLED);
        $isSolrEnabled = $this->searchData->isThirdPartSearchEngine() && $this->searchData->isActiveEngine();
        $isSuggestionsAllowed = ($isSolrEnabled && $isSearchSuggestionsEnabled);

        $result = [];

        if ($isSuggestionsAllowed) {
            foreach ($this->suggestions->getSearchSuggestions() as $suggestion) {
                $result[] = $this->queryResultFactory->create(
                    [
                        'queryText' => $suggestion['query_text'],
                        'resultsCount' => $suggestion['num_results'],
                    ]
                );
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isCountResultsEnabled()
    {
        return (bool)$this->searchData->getSolrConfigData(self::CONFIG_SUGGESTION_COUNT_RESULTS_ENABLED);
    }
}
