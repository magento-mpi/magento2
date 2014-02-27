<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Block;

 /**
 * Enterprise search suggestions block
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Suggestions extends \Magento\View\Element\Template
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Search\Model\Suggestions $suggestions
     * @param \Magento\Search\Helper\Data $searchData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Search\Model\Suggestions $suggestions,
        \Magento\Search\Helper\Data $searchData,
        array $data = array()
    ) {
        $this->_suggestions = $suggestions;
        $this->_searchData = $searchData;
        parent::__construct($context, $data);
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
     * @return bool
     */
    public function isCountResultsEnabled()
    {
        return (bool)$this->_searchData
            ->getSolrConfigData('server_suggestion_count_results_enabled');
    }
}
