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
 * Wishlist sidebar links container
 */
namespace Magento\FullPageCache\Model\Container;

class Wishlistlinks extends \Magento\FullPageCache\Model\Container\AbstractContainer
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_WISHLIST_ITEMS, '')
            . $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_WISHLINKS_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_placeholder->getAttribute('block');

        /** @var $block \Magento\Core\Block\Template */
        $block = \Mage::app()->getLayout()->createBlock($block);

        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }
}
