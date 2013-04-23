<?php
/**
 * Router for Magento web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Router_Rest
{
    /** @var array */
    protected $_routes = array();

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config_Rest $apiConfig
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config_Rest $apiConfig
    ) {
        $this->_helper = $helper;
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Route the Request, the only responsibility of the class.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     * @throws Mage_Webapi_Exception
     */
    public function match(Mage_Webapi_Controller_Request_Rest $request)
    {
        /** @var Mage_Webapi_Controller_Router_Route_Rest[] $routes */
        $routes = $this->_apiConfig->getAllRestRoutes();
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false
                && $route->getHttpMethod() == $request->getHttpMethod()
            ) {
                $request->setParams($params);
                /** Initialize additional request parameters using data from route */
                $request->setServiceName($route->getServiceName());
                $request->setMethodName($route->getMethodName());
                return $route;
            }
        }
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
