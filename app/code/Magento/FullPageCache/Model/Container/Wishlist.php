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
 * Wishlist sidebar container
 */
namespace Magento\FullPageCache\Model\Container;

class Wishlist extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST_ITEMS, '')
            . $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST, '')
            . ($this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_GROUP, ''));
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_WISHLIST_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_placeholder->getAttribute('block');
        $template = $this->_placeholder->getAttribute('template');

        /** @var $block \Magento\Core\Block\Template */
        $block = \Mage::app()->getLayout()->createBlock($block);
        $block->setTemplate($template);

        /** @var $blockPrice \Magento\Catalog\Block\Product\Price\Template */
        $blockPrice = \Mage::app()->getLayout()
            ->createBlock('Magento\Catalog\Block\Product\Price\Template', 'catalog_product_price_template');
        $blockPrice->addPriceBlockType('msrp', '\Magento\Catalog\Block\Product\Price', 'catalog/product/price_msrp.phtml');

        \Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));

        return $block->toHtml();
    }
}
