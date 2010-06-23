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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

 /**
 * Layered Navigation block for search
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Catalogsearch_Layer extends Mage_CatalogSearch_Block_Layer
{
    /**
     * Initialize blocks names
     */
    protected function _initBlocks()
    {
        parent::_initBlocks();

        if (Mage::helper('enterprise_search')->isActiveEngine()) {
            $this->_categoryBlockName = 'enterprise_search/catalog_layer_filter_category';
            $this->_attributeFilterBlockName = 'enterprise_search/catalog_layer_filter_attribute';
            $this->_priceFilterBlockName = 'enterprise_search/catalog_layer_filter_price';
            $this->_decimalFilterBlockName = 'enterprise_search/catalog_layer_filter_decimal';
        }
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Catalog_Block_Layer_View
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if (Mage::helper('enterprise_search')->getTaxInfluence()) {
            $this->unsetChild('price_filter');
        }

        return $this;
    }

    /**
     * Check availability display layer block
     *
     * @return bool
     */
    public function canShowBlock()
    {
        return $this->canShowOptions() || count($this->getLayer()->getState()->getFilters());
    }

    /**
     * Get layer object
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        if (Mage::helper('enterprise_search')->isActiveEngine()) {
            return Mage::getSingleton('enterprise_search/search_layer');
        }
        return parent::getLayer();
    }
}
