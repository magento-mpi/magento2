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
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @var \Magento\Search\Model\Suggestions
     */
    protected $_suggestions;

    /**
     * @param \Magento\Search\Model\Suggestions $suggestions
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Search\Model\Suggestions $suggestions,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_suggestions = $suggestions;
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

        $suggestions = $this->_suggestions->getSearchSuggestions();

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
