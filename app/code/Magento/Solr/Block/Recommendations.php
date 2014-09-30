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
class Recommendations extends \Magento\Framework\View\Element\Template
{
    /**
     * Search data
     *
     * @var \Magento\Solr\Helper\Data
     */
    protected $_searchData;

    /**
     * Recommendations factory
     *
     * @var \Magento\Solr\Model\RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Solr\Helper\Data $searchData
     * @param \Magento\Solr\Model\RecommendationsFactory $recommendationsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Solr\Helper\Data $searchData,
        \Magento\Solr\Model\RecommendationsFactory $recommendationsFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_searchData = $searchData;
        $this->_recommendationsFactory = $recommendationsFactory;
    }

    /**
     * Retrieve search recommendations
     *
     * @return array
     */
    public function getRecommendations()
    {
        $searchRecommendationsEnabled = (bool)$this->_searchData->getSearchConfigData(
            'search_recommendations_enabled'
        );

        if (!$searchRecommendationsEnabled) {
            return array();
        }

        /** @var \Magento\Solr\Model\Recommendations $recommendationsModel */
        $recommendationsModel = $this->_recommendationsFactory->create();
        $recommendations = $recommendationsModel->getSearchRecommendations();

        if (!count($recommendations)) {
            return array();
        }
        $result = array();

        foreach ($recommendations as $recommendation) {
            $result[] = array(
                'word' => $this->escapeHtml($recommendation['query_text']),
                'num_results' => $recommendation['num_results'],
                'link' => $this->getUrl("*/*/") . "?q=" . urlencode($recommendation['query_text'])
            );
        }
        return $result;
    }

    /**
     * Retrieve search recommendations count results enabled
     *
     * @return bool
     */
    public function isCountResultsEnabled()
    {
        return (bool)$this->_searchData->getSearchConfigData('search_recommendations_count_results_enabled');
    }
}
