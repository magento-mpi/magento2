<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\PageCache;

/**
 * Class Version
 *
 * @package Magento\Framework\App\PageCache
 */
class FormKey
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'form_key';

    /**
     * Cookie
     *
     * @var \Magento\Framework\Stdlib\Cookie
     */
    protected $cookie;

    /**
     * @param \Magento\Framework\Stdlib\Cookie $cookie
     */
    public function __construct(
        \Magento\Framework\Stdlib\Cookie $cookie
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
