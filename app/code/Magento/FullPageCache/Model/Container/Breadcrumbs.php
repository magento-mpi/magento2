<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Breadcrumbs container
 */
class Magento_FullPageCache_Model_Container_Breadcrumbs extends Magento_FullPageCache_Model_Container_Abstract
{
    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        if ($this->_getCategoryId() || $this->_getProductId()) {
            $cacheSubKey = '_' . $this->_getCategoryId() . '_' . $this->_getProductId();
        } else {
            $cacheSubKey = $this->_getRequestId();
        }

        return 'CONTAINER_BREADCRUMBS_' . md5($this->_placeholder->getAttribute('cache_id') . $cacheSubKey);
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();

        /** @var $product null|Magento_Catalog_Model_Product */
        $product = null;

        if ($productId) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                Mage::register('current_product', $product);
            }
        }
        $categoryId = $this->_getCategoryId();

        if ($product !== null && !$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
            Mage::unregister('current_category');
        }

        if ($categoryId && !Mage::registry('current_category')) {
            $category = Mage::getModel('Magento_Catalog_Model_Category')->load($categoryId);
            if ($category) {
                Mage::register('current_category', $category);
            }
        }

        //No need breadcrumbs on CMS pages
        if (!$productId && !$categoryId) {
            return '';
        }

        /** @var $breadcrumbsBlock Magento_Page_Block_Html_Breadcrumbs */
        $breadcrumbsBlock = $this->_getPlaceHolderBlock();
        $breadcrumbsBlock->setNameInLayout($this->_placeholder->getAttribute('name'));
        $crumbs = $this->_placeholder->getAttribute('crumbs');
        if ($crumbs) {
            $crumbs = unserialize(base64_decode($crumbs));
            foreach ($crumbs as $crumbName => $crumbInfo) {
                $breadcrumbsBlock->addCrumb($crumbName, $crumbInfo);
            }
        }

        Mage::dispatchEvent('render_block', array('block' => $breadcrumbsBlock, 'placeholder' => $this->_placeholder));
        return $breadcrumbsBlock->toHtml();
    }
}
