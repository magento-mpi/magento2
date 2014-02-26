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
    protected $cookie;

    /**
     * Request
     *
     * @var \Magento\App\Request\Http
     */
    protected $request;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\App\Request\Http $request
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Request\Http $request
    ) {
        $this->cookie = $cookie;
        $this->request = $request;
    }

    /**
     * Generate unique version identifier
     *
     * @return string
     */
    protected function generateValue()
    {
        return md5(rand() . time());
    }

    /**
     * Handle private content version cookie
     * Set cookie if it is not set.
     * Increment version on post requests.
     * In all other cases do nothing.
     *
     * @return void
     */
    public function process()
    {
        if ($this->request->isPost()) {
            $this->cookie->set(self::COOKIE_NAME, $this->generateValue(), self::COOKIE_PERIOD, '/');
        }
    }
}
