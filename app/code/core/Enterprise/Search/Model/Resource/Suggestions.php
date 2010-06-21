<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog search suggestions resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Resource_Suggestions extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Init main table
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_search/query', 'id');
    }


    /**
     * Retrieve search suggestions
     *
     * @param string $query
     * @return array
     */
    public function getSuggestionsByQuery($query, $params, $searchRecommendationsCount)
    {
        $searchEngineResourceModel   = Mage::getResourceModel('enterprise_search/engine');
        $searchSuggestionsEnabled    = (boolean)Mage::helper('enterprise_search')->getSolrConfigData("server_suggestion_enabled");
        $searchSuggestionsCount      = (int)Mage::helper('enterprise_search')->getSolrConfigData("server_suggestion_count");
        $searchSuggCountResEnabled   = (boolean)Mage::helper('enterprise_search')->getSolrConfigData("server_suggestion_count_results_enabled");
        $store = Mage::app()->getStore();
        $params["locale_code"]       = $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE);
        if ($searchSuggestionsCount < 1) {
            $searchSuggestionsCount = 1;
        }

        if ($searchSuggestionsEnabled) {
            return $searchEngineResourceModel->getSuggestionsByQuery($query, $params, $searchRecommendationsCount, $searchSuggCountResEnabled);
        } else {
            return array();
        }
    }
}
