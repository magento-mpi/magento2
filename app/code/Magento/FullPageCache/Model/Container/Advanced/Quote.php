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
namespace Magento\FullPageCache\Model\Container\Advanced;

abstract class Quote
    extends \Magento\FullPageCache\Model\Container\Advanced\AbstractAdvanced
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
        return static::CACHE_TAG_PREFIX . md5(self::_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CART, '')
            . self::_getCookieValue(\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER,  '')
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
