<?php
/**
 * Router for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Router
{
    /** @var array */
    protected $_routes = array();

    /** @var Magento_Webapi_Model_Rest_Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Rest_Config $apiConfig
     */
    public function __construct(Magento_Webapi_Model_Rest_Config $apiConfig)
    {
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Magento_Webapi_Controller_Rest_Request $request
     * @return Magento_Webapi_Controller_Rest_Router_Route
     * @throws Magento_Webapi_Exception
     */
    public function match(Magento_Webapi_Controller_Rest_Request $request)
    {
        /** @var Magento_Webapi_Controller_Rest_Router_Route[] $routes */
        $routes = $this->_apiConfig->getRestRoutes($request);
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                return $route;
            }
        }
        throw new Magento_Webapi_Exception(__('Request does not match any route.'), 0,
            Magento_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
