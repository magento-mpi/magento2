<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract Quote dependent container
 */
abstract class Magento_FullPageCache_Model_Container_Advanced_Quote
    extends Magento_FullPageCache_Model_Container_Advanced_Abstract
{
    /**
     * Cache tag prefix
     */
    const CACHE_TAG_PREFIX = 'quote_';

    /**
     * Get cache identifier
     *
     * @return string
     */
    public static function getCacheId()
    {
        return static::CACHE_TAG_PREFIX . md5(self::_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CART, '')
            . self::_getCookieValue(Magento_FullPageCache_Model_Cookie::COOKIE_CUSTOMER,  '')
        );
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return self::getCacheId();
    }

    /**
     * Get container individual additional cache id
     *
     * @return string
     */
    protected function _getAdditionalCacheId()
    {
        return md5($this->_placeholder->getName() . '_' . $this->_placeholder->getAttribute('cache_id'));
    }
}
