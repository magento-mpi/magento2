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
class Magento_Search_Block_Suggestions extends Magento_Core_Block_Template
{
    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSuggestions()
    {
        $helper = Mage::helper('Magento_Search_Helper_Data');

        $searchSuggestionsEnabled = (bool)$helper->getSolrConfigData('server_suggestion_enabled');
        if (!($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) || !$searchSuggestionsEnabled) {
            return array();
        }

        $suggestionsModel = Mage::getSingleton('Magento_Search_Model_Suggestions');
        $suggestions = $suggestionsModel->getSearchSuggestions();

        foreach ($suggestions as $key => $suggestion) {
            $suggestions[$key]['link'] = $this->getUrl('*/*/') . '?q=' . urlencode($suggestion['word']);
        }

        return $suggestions;
    }

    /**
     * Retrieve search suggestions count results enabled
     *
     * @return boolean
     */
    public function isCountResultsEnabled()
    {
        return (bool)Mage::helper('Magento_Search_Helper_Data')
            ->getSolrConfigData('server_suggestion_count_results_enabled');
    }
}
