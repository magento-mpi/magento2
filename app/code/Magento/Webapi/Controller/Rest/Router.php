<?php
/**
 * Router for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Rest;

class Router
{
    /** @var array */
    protected $_routes = array();

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
     * @param \Magento\Webapi\Controller\Rest\Request $request
     * @return \Magento\Webapi\Controller\Rest\Router\Route
     * @throws \Magento\Webapi\Exception
     */
    public function match(\Magento\Webapi\Controller\Rest\Request $request)
    {
        /** @var \Magento\Webapi\Controller\Rest\Router\Route[] $routes */
        $routes = $this->_apiConfig->getRestRoutes($request);
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                return $route;
            }
        }
        throw new \Magento\Webapi\Exception(__('Request does not match any route.'), 0,
            \Magento\Webapi\Exception::HTTP_NOT_FOUND);
    }
}
