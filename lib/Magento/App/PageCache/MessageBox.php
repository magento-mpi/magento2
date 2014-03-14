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
 * Class MsgBox
 *
 * @package Magento\App\PageCache
 */
class MessageBox
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'message_box_display';

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
     * Set Cookie for msg box when it displays first
     *
     * @return void
     */
    public function process()
    {
        if ($this->request->isPost()) {
            if ($this->cookie->get(self::COOKIE_NAME) === "0") {
                $this->cookie->set(self::COOKIE_NAME, null, 0, '/');
            } else {
                $this->cookie->set(self::COOKIE_NAME, 1, self::COOKIE_PERIOD, '/');
            }
        }
    }
}
