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
 * Cart sidebar container
 */
class Enterprise_PageCache_Model_Container_Sidebar_Cart extends Enterprise_PageCache_Model_Container_Advanced_Quote
{
    /**
     * @deprecated since 1.12.0.0
     */
    const CACHE_TAG_PREFIX = 'cartsidebar';

    /**
     * Get identifier from cookies
     *
     * @deprecated since 1.12.0.0
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CART, '')
            . $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        $renders = $this->_placeholder->getAttribute('item_renders');
        $block->deserializeRenders($renders);
        return $block->toHtml();
    }
}
