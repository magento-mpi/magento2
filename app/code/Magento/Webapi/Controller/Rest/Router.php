<?php
/**
 * Router for Magento web API.
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Webapi\Controller\Rest;

class Router
{
    /** @var array */
    protected $_routes = [];

    /** @var \Magento\Webapi\Model\Rest\Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Rest\Config $apiConfig
     */
    public function __construct(\Magento\Webapi\Model\Rest\Config $apiConfig)
    {
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Request $request
     * @return \Magento\Webapi\Controller\Rest\Router\Route
     * @throws \Magento\Webapi\Exception
     */
    public function match(Request $request)
    {
        /** @var \Magento\Webapi\Controller\Rest\Router\Route[] $routes */
        $routes = $this->_apiConfig->getRestRoutes($request);
        $matched = [];
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                $matched[] = $route;
            }
        }
        if (!empty($matched)) {
            return array_pop($matched);
        }
        throw new \Magento\Webapi\Exception(
            __('Request does not match any route.'),
            0,
            \Magento\Webapi\Exception::HTTP_NOT_FOUND
        );
    }
}
