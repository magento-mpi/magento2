<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Block;

/**
 * Enterprise search suggestions block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Suggestions extends \Magento\Framework\View\Element\Template
{
    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchData = null;

    /**
     * @var \Magento\Solr\Model\Suggestions
     */
    protected $_suggestions;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Solr\Model\Suggestions $suggestions
     * @param \Magento\Solr\Helper\Data $searchData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Solr\Model\Suggestions $suggestions,
        \Magento\Solr\Helper\Data $searchData,
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
        return (bool)$this->_searchData->getSolrConfigData('server_suggestion_count_results_enabled');
    }
}
