<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * Enterprise search suggestions model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Suggestions
{
    /**
     * Retrieve search suggestions
     *
     * @return array
     */
    public function getSearchSuggestions()
    {
        $productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection();
        $searchQueryText = Mage::helper('catalogsearch')->getQuery()->getQueryText();

        $params = array(
            'store_id' => $productCollection->getStoreId(),
        );

        $searchEngine = Mage::getStoreConfig('catalog/search/engine');
        if ($searchEngine != 'enterprise_search/engine') {
            return array();
        }

        $searchSuggestionsEnabled = Mage::helper('enterprise_search')->getSolrConfigData("server_suggestion_enabled");
        $searchSuggestionsCount   = (int)Mage::helper('enterprise_search')->getSolrConfigData("server_suggestion_count");
        if ($searchSuggestionsCount < 1) {
            $searchSuggestionsCount = 1;
        }
        if ($searchSuggestionsEnabled) {
            $model = Mage::getResourceModel('enterprise_search/suggestions');
            return $model->getSuggestionsByQuery($searchQueryText, $params, $searchSuggestionsCount);
        } else {
            return array();
        }
    }
}
