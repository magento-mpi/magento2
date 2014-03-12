<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\PageCache;

/**
 * Class Version
 *
 * @package Magento\App\PageCache
 */
class FormKey
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'form_key';

    /**
     * Ten years cookie period
     */
    const COOKIE_PERIOD = 315360000;

    /**
     * Cookie
     *
     * @var \Magento\Stdlib\Cookie
     */
    protected $cookie;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie
    ) {
        $this->cookie = $cookie;
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function get()
    {
        return $this->cookie->get(self::COOKIE_NAME);
    }
}
