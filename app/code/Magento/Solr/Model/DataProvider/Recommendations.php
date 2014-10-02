<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\DataProvider;

use Magento\Search\Model\QueryInterface;
use Magento\Search\Model\SearchDataProviderInterface;

class Recommendations implements SearchDataProviderInterface
{
    const CONFIG_SEARCH_RECOMMENDATIONS_ENABLED = 'search_recommendations_enabled';
    const CONFIG_SEARCH_RECOMMENDATIONS_COUNT_RESULTS_ENABLED = 'search_recommendations_count_results_enabled';

    /**
     * @var \Magento\Solr\Helper\Data
     */
    private $searchData;

    /**
     * @var \Magento\Solr\Model\RecommendationsFactory
     */
    private $recommendationsModelFactory;

    /**
     * @var \Magento\Search\Model\QueryResultFactory
     */
    private $queryResultFactory;

    /**
     * @param \Magento\Solr\Helper\Data $searchData
     * @param \Magento\Solr\Model\RecommendationsFactory $recommendationsModelFactory
     * @param \Magento\Search\Model\QueryResultFactory $queryResultFactory
     */
    public function __construct(
        \Magento\Solr\Helper\Data $searchData,
        \Magento\Solr\Model\RecommendationsFactory $recommendationsModelFactory,
        \Magento\Search\Model\QueryResultFactory $queryResultFactory
    ) {
        $this->searchData = $searchData;
        $this->recommendationsModelFactory = $recommendationsModelFactory;
        $this->queryResultFactory = $queryResultFactory;
    }

    /**
     * @return bool
     */
    public function isCountResultsEnabled()
    {
        return (bool)$this->searchData->getSearchConfigData(self::CONFIG_SEARCH_RECOMMENDATIONS_COUNT_RESULTS_ENABLED);
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter
     */
    public function getSearchData(QueryInterface $query, $limit = null, $additionalFilters = array())
    {
        $recommendations = array();

        if (!$this->isSearchRecommendationsEnabled()) {
            return array();
        }

        /** @var \Magento\Solr\Model\Recommendations $recommendationsModel */
        $recommendationsModel = $this->recommendationsModelFactory->create();

        foreach ($recommendationsModel->getSearchRecommendations() as $recommendation) {
            $recommendations[] = $this->queryResultFactory->create(
                [
                    'queryText' => $recommendation['query_text'],
                    'resultsCount' => $recommendation['num_results'],
                ]
            );
        }
        return $recommendations;
    }

    /**
     * @return bool
     */
    private function isSearchRecommendationsEnabled()
    {
        return (bool)$this->searchData->getSearchConfigData(self::CONFIG_SEARCH_RECOMMENDATIONS_ENABLED);
    }
}
