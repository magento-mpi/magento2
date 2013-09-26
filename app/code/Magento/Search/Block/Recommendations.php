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
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData;

    /**
     * Recommendations factory
     *
     * @var Magento_Search_Model_RecommendationsFactory
     */
    protected $_recommendationsFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_Search_Model_RecommendationsFactory $recommendationsFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Search_Helper_Data $searchData,
        Magento_Search_Model_RecommendationsFactory $recommendationsFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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

        /** @var Magento_Search_Model_Recommendations $recommendationsModel */
        $recommendationsModel = $this->_recommendationsFactory->create();
        $recommendations = $recommendationsModel->getSearchRecommendations();

        if (!count($recommendations)) {
            return array();
        }
        $result = array();

        /** @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper = $this->_coreData;
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
        return (boolean)$this->_searchData
            ->getSearchConfigData('search_recommendations_count_results_enabled');
    }
}
