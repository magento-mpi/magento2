<?php
/**
 * Router for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Router;

class Rest
{
    /** @var array */
    protected $_routes = array();

    /** @var \Magento\Webapi\Model\Config\Rest */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config\Rest $apiConfig
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Rest $apiConfig
    ) {
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param \Magento\Webapi\Controller\Request\Rest $request
     * @return \Magento\Webapi\Controller\Router\Route\Rest
     * @throws \Magento\Webapi\Exception
     */
    public function match(\Magento\Webapi\Controller\Request\Rest $request)
    {
        /** @var \Magento\Webapi\Controller\Router\Route\Rest[] $routes */
        $routes = $this->_apiConfig->getAllRestRoutes();
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                /** Initialize additional request parameters using data from route */
                $request->setResourceName($route->getResourceName());
                $request->setResourceType($route->getResourceType());
                return $route;
            }
        }
        throw new \Magento\Webapi\Exception(__('Request does not match any route.'),
            \Magento\Webapi\Exception::HTTP_NOT_FOUND);
    }

    /**
     * Check whether current request matches any route of specified method or not. Method version is taken into account.
     *
     * @param \Magento\Webapi\Controller\Request\Rest $request
     * @param string $methodName
     * @param string $version
     * @throws \Magento\Webapi\Exception In case when request does not match any route of specified method.
     */
    public function checkRoute(\Magento\Webapi\Controller\Request\Rest $request, $methodName, $version)
    {
        $resourceName = $request->getResourceName();
        $routes = $this->_apiConfig->getMethodRestRoutes($resourceName, $methodName, $version);
        foreach ($routes as $route) {
            if ($route->match($request)) {
                return;
            }
        }
        throw new \Magento\Webapi\Exception(__('Request does not match any route.'),
            \Magento\Webapi\Exception::HTTP_NOT_FOUND);
    }
}
