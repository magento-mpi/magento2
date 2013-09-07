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

    /** @var Magento_Webapi_Helper_Data */
    protected $_helper;

    /** @var Magento_Webapi_Model_Rest_Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Helper_Data $helper
     * @param Magento_Webapi_Model_Rest_Config $apiConfig
     */
    public function __construct(
        Magento_Webapi_Helper_Data $helper,
        Magento_Webapi_Model_Rest_Config $apiConfig
    ) {
        $this->_helper = $helper;
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
        $this->_matchVersion($request);
        /** @var Magento_Webapi_Controller_Rest_Router_Route[] $routes */
        $routes = $this->_apiConfig->getRestRoutes($request);
        foreach ($routes as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                $request->setParams($params);
                /** Initialize additional request parameters using data from route */
                // TODO: $request->setServiceId($route->getServiceId());
                // $request->setHttpMethod($route->getHttpMethod());
                // $request->setServiceVersion($route->getServiceVersion());
                return $route;
            }
        }
        throw new Magento_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Magento_Webapi_Exception::HTTP_NOT_FOUND);
    }

    /**
     * Extract version from path info and set it into request.
     * Remove version from path info if set.
     *
     * @param Magento_Webapi_Controller_Rest_Request $request
     */
    protected function _matchVersion(Magento_Webapi_Controller_Rest_Request $request)
    {
        $versionPattern = '/^\/(' . Magento_Webapi_Model_Config::VERSION_NUMBER_PREFIX .'\d+)/';
        preg_match($versionPattern, $request->getPathInfo(), $matches);
        if (isset($matches[1])) {
            $version = $matches[1];
            $request->setServiceVersion($version);
        }
    }
}
