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
 * Wishlist list container
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_FullPageCache_Model_Container_Wishlists extends Magento_FullPageCache_Model_Container_Abstract
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        return $this->_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER, '');
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return 'CONTAINER_WISHLISTS_' . md5($this->_getIdentifier());
    }

    /**
     * Retrieve cache identifier
     *
     * @return string
     */
    public function getCacheId()
    {
        return $this->_getCacheId();
    }

    /**
     * Render block content from placeholder
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        return $block->toHtml();
    }
}
