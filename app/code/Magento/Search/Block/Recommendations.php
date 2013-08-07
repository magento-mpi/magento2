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
 * Enterprise search suggestions block
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Block_Recommendations extends Magento_Core_Block_Template
{
    /**
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getRecommendations()
    {
        $searchRecommendationsEnabled = (boolean)Mage::helper('Magento_Search_Helper_Data')
            ->getSearchConfigData('search_recommendations_enabled');

        if (!$searchRecommendationsEnabled) {
            return array();
        }

        $recommendationsModel = Mage::getModel('Magento_Search_Model_Recommendations');
        $recommendations = $recommendationsModel->getSearchRecommendations();

        if (!count($recommendations)) {
            return array();
        }
        $result = array();

        /** @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper = Mage::helper('Magento_Core_Helper_Data');
        foreach ($recommendations as $recommendation) {
            $result[] = array(
                'word'        => $coreHelper->escapeHtml($recommendation['query_text']),
                'num_results' => $recommendation['num_results'],
                'link'        => $this->getUrl("*/*/") . "?q=" . urlencode($recommendation['query_text'])
            );
        }
        return $result;
    }

    /**
     * Retrieve search recommendations count results enabled
     *
     * @return boolean
     */
    public function isCountResultsEnabled()
    {
        return (boolean)Mage::helper('Magento_Search_Helper_Data')
            ->getSearchConfigData('search_recommendations_count_results_enabled');
    }
}
