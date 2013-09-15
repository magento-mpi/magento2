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
namespace Magento\FullPageCache\Model\Container;

class Breadcrumbs extends \Magento\FullPageCache\Model\Container\AbstractContainer
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

        /** @var $product null|\Magento\Catalog\Model\Product */
        $product = null;

        if ($productId) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                $this->_coreRegistry->register('current_product', $product);
            }
        }
        $categoryId = $this->_getCategoryId();

        if ($product !== null && !$product->canBeShowInCategory($categoryId)) {
            $categoryId = null;
            $this->_coreRegistry->unregister('current_category');
        }

        if ($categoryId && !$this->_coreRegistry->registry('current_category')) {
            $category = \Mage::getModel('Magento\Catalog\Model\Category')->load($categoryId);
            if ($category) {
                $this->_coreRegistry->register('current_category', $category);
            }
        }

        //No need breadcrumbs on CMS pages
        if (!$productId && !$categoryId) {
            return '';
        }

        /** @var $breadcrumbsBlock \Magento\Page\Block\Html\Breadcrumbs */
        $breadcrumbsBlock = $this->_getPlaceHolderBlock();
        $breadcrumbsBlock->setNameInLayout($this->_placeholder->getAttribute('name'));
        $crumbs = $this->_placeholder->getAttribute('crumbs');
        if ($crumbs) {
            $crumbs = unserialize(base64_decode($crumbs));
            foreach ($crumbs as $crumbName => $crumbInfo) {
                $breadcrumbsBlock->addCrumb($crumbName, $crumbInfo);
            }
        }

        $this->_eventManager->dispatch('render_block', array('block' => $breadcrumbsBlock, 'placeholder' => $this->_placeholder));
        return $breadcrumbsBlock->toHtml();
    }
}
