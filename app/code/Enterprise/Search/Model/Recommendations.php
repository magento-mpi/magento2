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
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getSearchRecommendations()
    {
        $productCollection = Mage::getSingleton('Enterprise_Search_Model_Search_Layer')->getProductCollection();
        $searchQueryText = Mage::helper('Magento_CatalogSearch_Helper_Data')->getQuery()->getQueryText();

        $params = array(
            'store_id' => $productCollection->getStoreId(),
        );

        $searchRecommendationsEnabled = (boolean)Mage::helper('Enterprise_Search_Helper_Data')
            ->getSearchConfigData('search_recommendations_enabled');
        $searchRecommendationsCount   = (int)Mage::helper('Enterprise_Search_Helper_Data')
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
