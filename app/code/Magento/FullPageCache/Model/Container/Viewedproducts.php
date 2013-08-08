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
 * Viewed products container
 */
class Magento_FullPageCache_Model_Container_Viewedproducts extends Magento_FullPageCache_Model_Container_Abstract
{
    const COOKIE_NAME = 'VIEWED_PRODUCT_IDS';

    /**
     * Get viewed product ids from cookie
     *
     * @return array
     */
    protected function _getProductIds()
    {
        $result = $this->_getCookieValue(self::COOKIE_NAME, array());
        if ($result) {
            $result = explode(',', $result);
        }
        return $result;
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        $cacheId = $this->_placeholder->getAttribute('cache_id');
        $productIds = $this->_getProductIds();
        if ($cacheId && $productIds) {
            sort($productIds);
            $cacheId = 'CONTAINER_' . md5($cacheId . implode('_', $productIds)
                . $this->_getCookieValue(Magento_Core_Model_Store::COOKIE_CURRENCY, ''));
            return $cacheId;
        }
        return false;
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        $block = $this->_getPlaceHolderBlock();
        $block->setProductIds($this->_getProductIds());
        Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
        return $block->toHtml();
    }
}
