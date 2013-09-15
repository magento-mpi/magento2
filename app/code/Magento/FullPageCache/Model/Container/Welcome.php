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
 * Welcome container
 */
namespace Magento\FullPageCache\Model\Container;

class Welcome extends \Magento\FullPageCache\Model\Container\Customer
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        $cacheId = $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER, '')
            . '_'
            . $this->_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_LOGGED_IN, '');
        return $cacheId;
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_WELCOME_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = \Mage::app()->getLayout()->createBlock('Magento\Page\Block\Html\Header');
        $this->_eventManager->dispatch('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->getWelcome();
    }
}
