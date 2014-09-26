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
    protected $_catalogSearchData = null;

    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @var \Magento\Catalog\Model\Layer\Search
     */
    protected $_searchLayer;

    /**
     * @var \Magento\Solr\Model\Resource\RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * @param \Magento\Solr\Model\Resource\RecommendationsFactory $recommendationsFactory
     * @param \Magento\Catalog\Model\Layer\Search $searchLayer
     * @param \Magento\Solr\Helper\Data $searchData
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        \Magento\Solr\Model\Resource\RecommendationsFactory $recommendationsFactory,
        \Magento\Catalog\Model\Layer\Search $searchLayer,
        \Magento\Solr\Helper\Data $searchData,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData
    ) {
        $this->_recommendationsFactory = $recommendationsFactory;
        $this->_searchLayer = $searchLayer;
        $this->_searchData = $searchData;
        $this->_catalogSearchData = $catalogSearchData;
    }

    /**
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getSearchRecommendations()
    {
        $productCollection = $this->_searchLayer->getProductCollection();
        $searchQueryText = $this->_catalogSearchData->getQuery()->getQueryText();

        $params = array('store_id' => $productCollection->getStoreId());

        $searchRecommendationsEnabled = (bool)$this->_searchData->getSearchConfigData(
            'search_recommendations_enabled'
        );
        $searchRecommendationsCount = (int)$this->_searchData->getSearchConfigData('search_recommendations_count');

        if ($searchRecommendationsCount < 1) {
            $searchRecommendationsCount = 1;
        }
        if ($searchRecommendationsEnabled) {
            /** @var \Magento\Solr\Model\Resource\Recommendations $recommendations */
            $recommendations = $this->_recommendationsFactory->create();
            return $recommendations->getRecommendationsByQuery(
                $searchQueryText,
                $params,
                $searchRecommendationsCount
            );
        } else {
            return array();
        }
    }
}
