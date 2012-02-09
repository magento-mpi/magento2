<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Breadcrumbs container
 */
class Enterprise_PageCache_Model_Container_Breadcrumbs extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        if ($this->_getCategoryId() || $this->_getProductId()) {
            $cacheSubKey = '_' . $this->_getCategoryId()
                . '_' . $this->_getProductId();
        } else {
            $cacheSubKey = $this->_getRequestId();
        }

        return 'CONTAINER_BREADCRUMBS_'
            . md5($this->_placeholder->getAttribute('cache_id') . $cacheSubKey);
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();
        if ($productId) {
            $product = Mage::getModel('Mage_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                Mage::register('current_product', $product);
            }
        }
        $categoryId = $this->_getCategoryId();
        if ($categoryId && !Mage::registry('current_category')) {
            $category = Mage::getModel('Mage_Catalog_Model_Category')->load($categoryId);
            if ($category) {
                Mage::register('current_category', $category);
            }
        }

        //No need breadcrumbs on CMS pages
        if (!$categoryId) {
            return '';
        }
        $breadcrumbsBlock = $this->_getPlaceHolderBlock();
        $breadcrumbsBlock->setNameInLayout('breadcrumbs');
        Mage::dispatchEvent('render_block', array('block' => $breadcrumbsBlock, 'placeholder' => $this->_placeholder));
        return $breadcrumbsBlock->toHtml();
    }
}
