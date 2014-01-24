<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page cache data helper
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Model;

/**
 * Class Data
 * @package Magento\PageCache\Model
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
        \Magento\App\RequestInterface $request
    ) {
        $this->cookie = $cookie;
        $this->request = $request;
    }

    /**
     * Retrieve content version.
     * Returns false if cookie is not set
     *
     * @return string|bool
     */
    private function get()
    {
        return $this->cookie->get(self::COOKIE_NAME);
    }

    /**
     * Set base private content version cookie (for new users)
     */
    private function set()
    {
        $this->cookie->set(self::COOKIE_NAME, 0, self::COOKIE_PERIOD, '/');
    }

    /**
     * Increment private content version cookie (for user to pull new private content)
     */
    private function increment()
    {
        $newVersion = 1 + $this->cookie->get(self::COOKIE_NAME);
        $this->cookie->set(self::COOKIE_NAME, $newVersion, self::COOKIE_PERIOD, '/');
    }

    /**
     * Handle private content version cookie
     * Set cookie if it is not set.
     * Increment version on post requests.
     * In all other cases do nothing.
     */
    public function process()
    {
        if (false === $this->get()) {
            $this->set();
        } else {
            if ($this->request->isPost()) {
                $this->increment();
            }
        }
    }
}
