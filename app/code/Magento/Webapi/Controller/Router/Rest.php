<?php
/**
 * Router for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Router_Rest
{
    /** @var array */
    protected $_routes = array();

    /** @var Magento_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Rest $apiConfig
     */
    public function __construct(
        Magento_Webapi_Model_Config_Rest $apiConfig
    ) {
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Magento_Webapi_Controller_Request_Rest $request
     * @return Magento_Webapi_Controller_Router_Route_Rest
     * @throws Magento_Webapi_Exception
     */
    public function match(Magento_Webapi_Controller_Request_Rest $request)
    {
        /** @var Magento_Webapi_Controller_Router_Route_Rest[] $routes */
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
        throw new Magento_Webapi_Exception(__('Request does not match any route.'),
            Magento_Webapi_Exception::HTTP_NOT_FOUND);
    }

    /**
     * Check whether current request matches any route of specified method or not. Method version is taken into account.
     *
     * @param Magento_Webapi_Controller_Request_Rest $request
     * @param string $methodName
     * @param string $version
     * @throws Magento_Webapi_Exception In case when request does not match any route of specified method.
     */
    public function checkRoute(Magento_Webapi_Controller_Request_Rest $request, $methodName, $version)
    {
        $resourceName = $request->getResourceName();
        $routes = $this->_apiConfig->getMethodRestRoutes($resourceName, $methodName, $version);
        foreach ($routes as $route) {
            if ($route->match($request)) {
                return;
            }
        }
        throw new Magento_Webapi_Exception(__('Request does not match any route.'),
            Magento_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
