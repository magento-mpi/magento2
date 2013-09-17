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
 * Cart sidebar container
 */
class Magento_FullPageCache_Model_Container_Sidebar_Cart extends Magento_FullPageCache_Model_Container_Advanced_Quote
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
