<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\PageCache;

/**
 * Class Version
 *
 */
class FormKey
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'form_key';

    /**
     * CookieManager
     *
     * @var \Magento\Framework\Stdlib\CookieManager
     */
    protected $cookieManager;

    /**
     * @param \Magento\Framework\Stdlib\CookieManager $cookieManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\CookieManager $cookieManager
    ) {
        $this->cookieManager = $cookieManager;
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function get()
    {
        return $this->cookieManager->getCookie(self::COOKIE_NAME);
    }
}
