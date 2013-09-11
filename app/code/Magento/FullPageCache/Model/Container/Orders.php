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
 * Orders container
 */
namespace Magento\FullPageCache\Model\Container;

class Orders extends \Magento\FullPageCache\Model\Container\Advanced\AbstractAdvanced
{
    const CACHE_TAG_PREFIX = 'orders';

    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return md5($this->_getIdentifier());
    }

    /**
     * Get container individual additional cache id
     *
     * @return string | false
     */
    protected function _getAdditionalCacheId()
    {
        return md5('CONTAINER_ORDERS_' . $this->_placeholder->getAttribute('cache_id'));
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        \Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }
}
