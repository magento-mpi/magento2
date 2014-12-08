<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model;

/**
 * Enterprise search recommendations model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Recommendations
{
    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $queryFactory = null;

    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $searchData = null;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $searchLayer;

    /**
     * @var \Magento\Solr\Model\Resource\RecommendationsFactory
     */
    protected $recommendationsFactory;

    /**
     * @param \Magento\Solr\Model\Resource\RecommendationsFactory $recommendationsFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Solr\Helper\Data $searchData
     * @param \Magento\Search\Model\QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\Solr\Model\Resource\RecommendationsFactory $recommendationsFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Solr\Helper\Data $searchData,
        \Magento\Search\Model\QueryFactory $queryFactory
    ) {
        $this->recommendationsFactory = $recommendationsFactory;
        $this->searchLayer = $layerResolver->get();
        $this->searchData = $searchData;
        $this->queryFactory = $queryFactory;
    }

    /**
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getSearchRecommendations()
    {
        $productCollection = $this->searchLayer->getProductCollection();
        $searchQueryText = $this->queryFactory->get()->getQueryText();

        $params = ['store_id' => $productCollection->getStoreId()];

        $searchRecommendationsEnabled = (bool)$this->searchData->getSearchConfigData(
            'search_recommendations_enabled'
        );
        $searchRecommendationsCount = (int)$this->searchData->getSearchConfigData('search_recommendations_count');

        if ($searchRecommendationsCount < 1) {
            $searchRecommendationsCount = 1;
        }
        if ($searchRecommendationsEnabled) {
            /** @var \Magento\Solr\Model\Resource\Recommendations $recommendations */
            $recommendations = $this->recommendationsFactory->create();
            return $recommendations->getRecommendationsByQuery(
                $searchQueryText,
                $params,
                $searchRecommendationsCount
            );
        } else {
            return [];
        }
    }
}
