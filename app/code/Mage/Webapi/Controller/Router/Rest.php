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

    /** @var Mage_Webapi_Config */
    protected $_apiConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Config $apiConfig
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Config $apiConfig
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
        // get path info and fetch service and version
        $pathInfo = $request->getPathInfo();
        $urlDelimiter = '/';
        $path = explode($urlDelimiter, $pathInfo);

        // uri's will be of pattern webapi/rest/<version>/<service-name>/...
        $version = $path[3];
        $serviceBaseUrl = $path[4];

        /** @var Mage_Webapi_Controller_Router_Route_Rest[] $routes */
        $routes = $this->_apiConfig->getRestRoutes(
            $request->getHttpMethod(),
            $urlDelimiter . $serviceBaseUrl,
            $version
        );
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
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
