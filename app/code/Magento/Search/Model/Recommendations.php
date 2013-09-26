<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
  * Enterprise search recommendations model
  *
  * @SuppressWarnings(PHPMD.LongVariable)
  */
namespace Magento\Search\Model;

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
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @var \Magento\Search\Model\Search\Layer
     */
    protected $_searchLayer;

    /**
     * @var \Magento\Search\Model\Resource\RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * @param \Magento\Search\Model\Resource\RecommendationsFactory $recommendationsFactory
     * @param \Magento\Search\Model\Search\Layer $searchLayer
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     */
    public function __construct(
        \Magento\Search\Model\Resource\RecommendationsFactory $recommendationsFactory,
        \Magento\Search\Model\Search\Layer $searchLayer,
        \Magento\Search\Helper\Data $searchData,
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

        $params = array(
            'store_id' => $productCollection->getStoreId(),
        );

        $searchRecommendationsEnabled = (boolean)$this->_searchData
            ->getSearchConfigData('search_recommendations_enabled');
        $searchRecommendationsCount   = (int)$this->_searchData
            ->getSearchConfigData('search_recommendations_count');

        if ($searchRecommendationsCount < 1) {
            $searchRecommendationsCount = 1;
        }
        if ($searchRecommendationsEnabled) {
            return $this->_recommendationsFactory->create()
                ->getRecommendationsByQuery($searchQueryText, $params, $searchRecommendationsCount);
        } else {
            return array();
        }
    }
}
