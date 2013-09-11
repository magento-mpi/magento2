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
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Model;

class Recommendations
{
    /**
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getSearchRecommendations()
    {
        $productCollection = \Mage::getSingleton('Magento\Search\Model\Search\Layer')->getProductCollection();
        $searchQueryText = \Mage::helper('Magento\CatalogSearch\Helper\Data')->getQuery()->getQueryText();

        $params = array(
            'store_id' => $productCollection->getStoreId(),
        );

        $searchRecommendationsEnabled = (boolean)\Mage::helper('Magento\Search\Helper\Data')
            ->getSearchConfigData('search_recommendations_enabled');
        $searchRecommendationsCount   = (int)\Mage::helper('Magento\Search\Helper\Data')
            ->getSearchConfigData('search_recommendations_count');

        if ($searchRecommendationsCount < 1) {
            $searchRecommendationsCount = 1;
        }
        if ($searchRecommendationsEnabled) {
            $model = \Mage::getResourceModel('Magento\Search\Model\Resource\Recommendations');
            return $model->getRecommendationsByQuery($searchQueryText, $params, $searchRecommendationsCount);
        } else {
            return array();
        }
    }
}
