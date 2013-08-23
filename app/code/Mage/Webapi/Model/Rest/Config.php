<?php
/**
 * Webapi Config Model for Rest.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Rest_Config extends Mage_Webapi_Model_Config
{
    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /**
     * @param Mage_Core_Model_Config $config
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     */
    public function __construct(
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Controller_Router_Route_Factory $routeFactory
    ) {
        parent::__construct($config, $configCacheType, $moduleReader);
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
     *      'serviceId' => 'Mage_Catalog_Service_CategoryService',
     *      'serviceMethod' => 'item'
     *      'secure' => true
     *  );</pre>
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _createRoute($routeData)
    {
        /** @var $route Mage_Webapi_Controller_Router_Route_Rest */
        $route = $this->_routeFactory->createRoute(
            'Mage_Webapi_Controller_Router_Route_Rest',
            strtolower($routeData['routePath'])
        );

        $route->setServiceId($routeData['serviceId'])
            ->setHttpMethod($routeData['httpMethod'])
            ->setServiceMethod($routeData['serviceMethod'])
            ->setServiceVersion(self::VERSION_NUMBER_PREFIX . $routeData['version'])
            ->setSecure($routeData[self::SECURE_ATTR_NAME]);
        return $route;
    }

    /**
     * Get service base URL
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return string|null
     */
    protected function _getServiceBaseUrl($request)
    {
        $baseUrlRegExp = '#^/\w+/\w+#';
        $serviceBaseUrl = preg_match($baseUrlRegExp, $request->getPathInfo(), $matches) ? $matches[0] : null;

        return $serviceBaseUrl;
    }

    /**
     * Generate the list of available REST routes.
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return array
     * @throws Mage_Webapi_Exception
     */
    public function getRestRoutes(Mage_Webapi_Controller_Request_Rest $request)
    {
        $serviceBaseUrl = $this->_getServiceBaseUrl($request);
        $httpMethod = $request->getHttpMethod();
        $routes = array();
        foreach ($this->getRestServices() as $serviceName => $serviceData) {
            // skip if baseurl is not null and does not match
            if (!isset($serviceData['baseUrl'])
                || (isset($serviceBaseUrl) && (strtolower($serviceBaseUrl) != strtolower($serviceData['baseUrl'])))
            ) {
                // baseurl does not match, just skip this service
                continue;
            }
            // TODO: skip if version is not null and does not match
            foreach ($serviceData[self::KEY_OPERATIONS] as $operationName => $operationData) {
                if (strtoupper($operationData['httpMethod']) == strtoupper($httpMethod)) {
                    $secure = isset($operationData[self::SECURE_ATTR_NAME])
                        ? $operationData[self::SECURE_ATTR_NAME]
                        : false;
                    $methodRoute = isset($operationData['route']) ? $operationData['route'] : '';
                    $routes[] = $this->_createRoute(
                        array(
                            'routePath' => $serviceData['baseUrl'] . $methodRoute,
                            'version' => $request->getServiceVersion(), // TODO: Take version from config
                            'serviceId' => $serviceName,
                            'serviceMethod' => $operationName,
                            'httpMethod' => $httpMethod,
                            self::SECURE_ATTR_NAME => $secure
                        )
                    );
                }
            }
        }

        return $routes;
    }
}