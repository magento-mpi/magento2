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
     * Cache tag prefix
     */
    const CACHE_TAG_PREFIX = 'cartsidebar';

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
