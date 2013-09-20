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
class Magento_Search_Model_Recommendations
{
    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @var Magento_Search_Model_Search_Layer
     */
    protected $_searchLayer;

    /**
     * @var Magento_Search_Model_Resource_RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * @param Magento_Search_Model_Resource_RecommendationsFactory $recommendationsFactory
     * @param Magento_Search_Model_Search_Layer $searchLayer
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     */
    public function __construct(
        Magento_Search_Model_Resource_RecommendationsFactory $recommendationsFactory,
        Magento_Search_Model_Search_Layer $searchLayer,
        Magento_Search_Helper_Data $searchData,
        Magento_CatalogSearch_Helper_Data $catalogSearchData
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
