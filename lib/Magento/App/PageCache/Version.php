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
class Version
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'private_content_version';

    /**
     * Ten years cookie period
     */
    const COOKIE_PERIOD = 315360000;

    /**
     * Cookie
     *
     * @var \Magento\Stdlib\Cookie
     */
    private $cookie;

    /**
     * Request
     *
     * @var \Magento\App\Request\Http
     */
    private $request;

    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Request\Http $request
    ) {
        $this->cookie = $cookie;
        $this->request = $request;
    }

    /**
     * Increment private content version cookie (for user to pull new private content)
     */
    private function set()
    {
        $this->cookie->set(self::COOKIE_NAME, $this->generateValue(), self::COOKIE_PERIOD, '/');
    }

    /**
     * Generate unique version identifier
     *
     * @return string
     */
    private function generateValue()
    {
        return md5(rand() . time());
    }

    /**
     * Handle private content version cookie
     * Set cookie if it is not set.
     * Increment version on post requests.
     * In all other cases do nothing.
     */
    public function process()
    {
        if ($this->request->isPost()) {
            $this->set();
        }
    }
}
