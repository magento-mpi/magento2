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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Catalog layered navigation view block
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        Mage::register('current_layer', $this->getLayer());
    }

    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();
        if ($this->getIsEngineAvailable()) {
            $this->_categoryBlockName = 'enterprise_search/catalog_layer_filter_category';
            $this->_attributeFilterBlockName = 'enterprise_search/catalog_layer_filter_attribute';
            $this->_priceFilterBlockName = 'enterprise_search/catalog_layer_filter_price';
            $this->_decimalFilterBlockName = 'enterprise_search/catalog_layer_filter_decimal';
        }
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if ($this->getIsEngineAvailable()) {
            return Mage::getSingleton('enterprise_search/catalog_layer');
        }
        return parent::getLayer();
    }

    /**
     * Check if search engine gen be used for catalog navigation
     *
     * @return bool
     */
    public function getIsEngineAvailable()
    {
        if (!$this->hasData('is_engine_available')) {
            $available = Mage::helper('enterprise_search')->isActiveEngine()
                && Mage::helper('enterprise_search')->getSearchConfigData('solr_server_use_in_catalog_navigation')
                && !Mage::helper('enterprise_search')->getTaxInfluence();
            $this->setData('is_engine_available', $available);
        }
        return $this->_getData('is_engine_available');
    }
}
