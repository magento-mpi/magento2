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
class Recommendations extends \Magento\View\Element\Template
{
    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    /**
     * Recommendations factory
     *
     * @var \Magento\Search\Model\RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Search\Model\RecommendationsFactory $recommendationsFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Search\Model\RecommendationsFactory $recommendationsFactory,
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
        $searchRecommendationsEnabled = (boolean)$this->_searchData
            ->getSearchConfigData('search_recommendations_enabled');

        if (!$searchRecommendationsEnabled) {
            return array();
        }

        /** @var \Magento\Search\Model\Recommendations $recommendationsModel */
        $recommendationsModel = $this->_recommendationsFactory->create();
        $recommendations = $recommendationsModel->getSearchRecommendations();

        if (!count($recommendations)) {
            return array();
        }
        $result = array();

        foreach ($recommendations as $recommendation) {
            $result[] = array(
                'word'        => $this->escapeHtml($recommendation['query_text']),
                'num_results' => $recommendation['num_results'],
                'link'        => $this->getUrl("*/*/") . "?q=" . urlencode($recommendation['query_text'])
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
        return (boolean)$this->_searchData
            ->getSearchConfigData('search_recommendations_count_results_enabled');
    }
}
