<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\ProductList;

/**
 * Class Toolbar
 * @package Magento\Catalog\Model\Product\ProductList
 */
class Toolbar
{
    /**
     * GET parameter page variable name
     */
    const PAGE_PARM_NAME = 'p';

    /**
     * Sort order cookie name
     */
    const ORDER_COOKIE_NAME = 'product_list_order';

    /**
     * Sort direction cookie name
     */
    const DIRECTION_COOKIE_NAME = 'product_list_dir';

    /**
     * Sort mode cookie name
     */
    const MODE_COOKIE_NAME = 'product_list_mode';

    /**
     * Products per page limit order cookie name
     */
    const LIMIT_COOKIE_NAME = 'product_list_limit';

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
     * Get sort order
     *
     * @return string|bool
     */
    public function getOrder()
    {
        return $this->cookie->get(self::ORDER_COOKIE_NAME);
    }

    /**
     * Get sort direction
     *
     * @return string|bool
     */
    public function getDirection()
    {
        return $this->cookie->get(self::DIRECTION_COOKIE_NAME);
    }

    /**
     * Get sort mode
     *
     * @return string|bool
     */
    public function getMode()
    {
        return $this->cookie->get(self::MODE_COOKIE_NAME);
    }

    /**
     * Get products per page limit
     *
     * @return string|bool
     */
    public function getLimit()
    {
        return $this->cookie->get(self::LIMIT_COOKIE_NAME);
    }
    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $page = (int) $this->request->getParam(self::PAGE_PARM_NAME);
        return $page ? $page : 1;
    }
}
