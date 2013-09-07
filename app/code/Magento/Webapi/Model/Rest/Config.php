<?php
/**
 * Webapi Config Model for Rest.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Rest_Config
{
    /**#@+
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_POST = 'POST';
    /**#@-*/

    /**#@+
     * Keys that a used for config internal representation.
     */
    const KEY_IS_SECURE = 'isSecure';
    const KEY_CLASS = 'class';
    const KEY_HTTP_METHOD = 'httpMethod';
    const KEY_METHOD = 'method';
    const KEY_VERSION = 'version';
    const KEY_ROUTE_PATH = 'routePath';
    /*#@-*/

    /** @var Magento_Webapi_Model_Config  */
    protected $_config;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /**
     * @param Magento_Webapi_Model_Config
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     */
    public function __construct(
        Magento_Webapi_Model_Config $config,
        Magento_Controller_Router_Route_Factory $routeFactory
    ) {
        $this->_config = $config;
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Create route object.
     *
     * @param array $routeData Expected format:
     *  <pre>array(
     *      'routePath' => '/categories/:categoryId',
     *      'httpMethod' => 'GET',
     *      'version' => 1,
     *      'class' => 'Mage_Catalog_Service_CategoryService',
     *      'serviceMethod' => 'item'
     *      'secure' => true
     *  );</pre>
     * @return Magento_Webapi_Controller_Rest_Router_Route
     */
    protected function _createRoute($routeData)
    {
        /** @var $route Magento_Webapi_Controller_Rest_Router_Route */
        $route = $this->_routeFactory->createRoute(
            'Mage_Webapi_Controller_Rest_Router_Route',
            strtolower($routeData[self::KEY_ROUTE_PATH])
        );

        $route->setServiceId($routeData[self::KEY_CLASS])
            ->setHttpMethod($routeData[self::KEY_HTTP_METHOD])
            ->setServiceMethod($routeData[self::KEY_METHOD])
            ->setServiceVersion(Magento_Webapi_Model_Config::VERSION_NUMBER_PREFIX . $routeData[self::KEY_VERSION])
            ->setSecure($routeData[self::KEY_IS_SECURE]);
        return $route;
    }

    /**
     * Get service base URL
     *
     * @param Magento_Webapi_Controller_Rest_Request $request
     * @return string|null
     */
    protected function _getServiceBaseUrl($request)
    {
        $baseUrlRegExp = '#^/?\w+/\w+#';
        $serviceBaseUrl = preg_match($baseUrlRegExp, $request->getPathInfo(), $matches) ? $matches[0] : null;

        return $serviceBaseUrl;
    }

    /**
     * Generate the list of available REST routes.
     *
     * @param Magento_Webapi_Controller_Rest_Request $request
     * @return array
     * @throws Magento_Webapi_Exception
     */
    public function getRestRoutes(Magento_Webapi_Controller_Rest_Request $request)
    {
        $serviceBaseUrl = $this->_getServiceBaseUrl($request);
        $httpMethod = $request->getHttpMethod();
        $routes = array();
        foreach ($this->_config->getServices() as $serviceName => $serviceData) {
            // skip if baseurl is not null and does not match
            if (
                !isset($serviceData[Magento_Webapi_Model_Config::ATTR_SERVICE_PATH])
                || !$serviceBaseUrl
                || strcasecmp(
                    trim($serviceBaseUrl, '/'),
                    trim($serviceData[Magento_Webapi_Model_Config::ATTR_SERVICE_PATH], '/')
                ) !== 0
            ) {
                // baseurl does not match, just skip this service
                continue;
            }
            // TODO: skip if version is not null and does not match
            foreach ($serviceData['methods'] as $methodName => $methodInfo) {
                if (strtoupper($methodInfo[Magento_Webapi_Model_Config::ATTR_HTTP_METHOD]) == strtoupper($httpMethod)) {
                    $secure = isset($methodInfo[Magento_Webapi_Model_Config::ATTR_IS_SECURE])
                        ? $methodInfo[Magento_Webapi_Model_Config::ATTR_IS_SECURE] : false;
                    $methodRoute = isset($methodInfo['route']) ? $methodInfo['route'] : '';
                    $routes[] = $this->_createRoute(
                        array(
                            self::KEY_ROUTE_PATH =>
                                $serviceData[Magento_Webapi_Model_Config::ATTR_SERVICE_PATH] . $methodRoute,
                            self::KEY_VERSION => $request->getServiceVersion(), // TODO: Take version from config
                            self::KEY_CLASS => $serviceName,
                            self::KEY_METHOD => $methodName,
                            self::KEY_HTTP_METHOD => $httpMethod,
                            self::KEY_IS_SECURE => $secure
                        )
                    );
                }
            }
        }

        return $routes;
    }
}
