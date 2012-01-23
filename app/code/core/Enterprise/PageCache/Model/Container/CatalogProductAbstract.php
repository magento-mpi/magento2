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
 * @package     Enterprise_PageCache
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Abstract placeholder container for catalog product blocks
 */
abstract class Enterprise_PageCache_Model_Container_CatalogProductAbstract
    extends Enterprise_PageCache_Model_Container_Advanced_Abstract
{
    /**
     * Get container individual additional cache id
     *
     * @return string | false
     */
    protected function _getAdditionalCacheId()
    {
        $websiteId = $this->_placeholder->getAttribute('website_id');
        $segmentIds = Enterprise_PageCache_Model_Cookie::getCustomerSegmentsIds($websiteId);
        return 'SEGMENTS_LIST_' . $segmentIds;
    }

    protected function _renderBlock()
    {
        $this->_loadProductToRegister();
        $block = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');
        $block = new $block;
        $block->setTemplate($template);
        $block->setLayout(Mage::app()->getLayout());
        $block->setChild($this->_getItemsBlockAlias(), $this->_getItemsBlock());
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        $block->setSkipRenderTag(true);
        return $block->toHtml();
    }

    /**
     * Get Items Block
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _getItemsBlock()
    {
        $itemBlock = Mage::app()->getLayout()->createBlock('enterprise_targetrule/catalog_product_item');
        $itemBlock->setTemplate($this->_getItemsBlockTemplatePath());
        $itemBlock->setLayout(Mage::app()->getLayout());
        $itemBlock->setNameInLayout($this->_getItemsBlockAlias());
        $itemBlock->setIsAnonymous(false);
        return $itemBlock;
    }

    /**
     * Load Product To Register
     */
    protected function _loadProductToRegister()
    {
        $productId = $this->_getProductId();
        if ($productId && !Mage::registry('product')) {
            $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                Mage::register('product', $product);
            }
        }
    }

    /**
     * Get Items Block Alias
     *
     * @abstract
     * @return string
     */
    abstract protected function _getItemsBlockAlias();

    /**
     * Get Items Block Template Path
     *
     * @abstract
     * @return string
     */
    abstract protected function _getItemsBlockTemplatePath();
}
