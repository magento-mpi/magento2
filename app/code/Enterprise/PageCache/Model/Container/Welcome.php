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
 * Welcome container
 */
class Enterprise_PageCache_Model_Container_Welcome extends Enterprise_PageCache_Model_Container_Customer
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        $cacheId = $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '')
            . '_'
            . $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN, '');
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
        $block = Mage::app()->getLayout()->createBlock('Magento_Page_Block_Html_Header');
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->getWelcome();
    }
}
