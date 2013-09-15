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
namespace Magento\Search\Block;

class Suggestions extends \Magento\Core\Block\Template
{
    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Search_Helper_Data $searchData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSuggestions()
    {
        $helper = $this->_searchData;

        $searchSuggestionsEnabled = (bool)$helper->getSolrConfigData('server_suggestion_enabled');
        if (!($helper->isThirdPartSearchEngine() && $helper->isActiveEngine()) || !$searchSuggestionsEnabled) {
            return array();
        }

        $suggestionsModel = \Mage::getSingleton('Magento\Search\Model\Suggestions');
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
        return (bool)$this->_searchData
            ->getSolrConfigData('server_suggestion_count_results_enabled');
    }
}
