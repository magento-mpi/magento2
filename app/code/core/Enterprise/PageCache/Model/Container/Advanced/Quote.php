<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract Quote dependent container
 */
abstract class Enterprise_PageCache_Model_Container_Advanced_Quote
    extends Enterprise_PageCache_Model_Container_Advanced_Abstract
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
        $cookieCart = Enterprise_PageCache_Model_Cookie::COOKIE_CART;
        $cookieCustomer = Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER;
        return md5(Enterprise_PageCache_Model_Container_Advanced_Quote::CACHE_TAG_PREFIX
            . (array_key_exists($cookieCart, $_COOKIE) ? $_COOKIE[$cookieCart] : '')
            . (array_key_exists($cookieCustomer, $_COOKIE) ? $_COOKIE[$cookieCustomer] : ''));
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return Enterprise_PageCache_Model_Container_Advanced_Quote::getCacheId();
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
