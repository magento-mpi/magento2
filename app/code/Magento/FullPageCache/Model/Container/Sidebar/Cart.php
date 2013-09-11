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
namespace Magento\FullPageCache\Model\Container\Sidebar;

class Cart extends \Magento\FullPageCache\Model\Container\Advanced\Quote
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
