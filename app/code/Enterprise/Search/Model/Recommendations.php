<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise search recommendations model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Recommendations
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
     * @var Enterprise_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @param Enterprise_Search_Helper_Data $searchData
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     */
    public function __construct(
        Enterprise_Search_Helper_Data $searchData,
        Magento_CatalogSearch_Helper_Data $catalogSearchData
    ) {
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
        $productCollection = Mage::getSingleton('Enterprise_Search_Model_Search_Layer')->getProductCollection();
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
            $model = Mage::getResourceModel('Enterprise_Search_Model_Resource_Recommendations');
            return $model->getRecommendationsByQuery($searchQueryText, $params, $searchRecommendationsCount);
        } else {
            return array();
        }
    }
}
