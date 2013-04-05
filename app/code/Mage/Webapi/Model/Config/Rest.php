<?php
/**
 * REST specific API config.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Rest
{
    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     * @param Mage_Core_Model_App $application
     */
    public function __construct(
        Mage_Core_Service_Config $serviceConfig,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Core_Model_App $application
    ) {
        $this->_serviceConfig = $serviceConfig;
        $this->_routeFactory = $routeFactory;
        $this->_application = $application;
    }

    /**
     * Get all modules routes defined in config.
     *
     * @return Mage_Webapi_Controller_Router_Route_Rest[]
     * @throws LogicException When config data has invalid structure.
     */
    public function getAllRestRoutes()
    {
        $routes = array();
        foreach ($this->_getRestRoutesData() as $routePath => $routeData) {
            $routes[] = $this->_createRoute(
                $routePath,
                $routeData['serviceName'],
                $routeData['methodName'],
                $routeData['httpMethod']
            );
        }
        return $routes;
    }

    /**
     * Retrieve routes data from service registry.
     *
     * @return array
     */
    protected function _getRestRoutesData()
    {
        $serviceData = $this->_serviceConfig->getData();
        $routesData = isset($serviceData['rest_routes']) && is_array($serviceData['rest_routes'])
            ? $serviceData['rest_routes']
            : array();
        return $routesData;
    }

    /**
     * Identify the shortest available route to the item of specified resource.
     *
     * @param string $serviceName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getRestRouteToItem($serviceName)
    {
        $routesData = $this->_getRestRoutesData();
        /** The shortest routes must go first. */
        ksort($routesData);
        foreach ($routesData as $routePath => $routeMetadata) {
            // TODO: Ensure that it works correctly with Item and Collection
            if ($routeMetadata['httpMethod'] == Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_GET
                && $routeMetadata['serviceName'] == $serviceName
            ) {
                return $routePath;
            }
        }
        throw new InvalidArgumentException(sprintf('No route to the item of "%s" resource was found.', $serviceName));
    }

    /**
     * Create route object.
     *
     * @param string $routePath
     * @param string $serviceName
     * @param string $methodName
     * @param string $httpMethod
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _createRoute($routePath, $serviceName, $methodName, $httpMethod)
    {
        $apiTypeRoutePath = $this->_application->getConfig()->getAreaFrontName()
            . '/:' . Mage_Webapi_Controller_Front::API_TYPE_REST;
        $fullRoutePath = $apiTypeRoutePath . $routePath;
        /** @var $route Mage_Webapi_Controller_Router_Route_Rest */
        $route = $this->_routeFactory->createRoute('Mage_Webapi_Controller_Router_Route_Rest', $fullRoutePath);
        $route->setServiceName($serviceName)->setHttpMethod($httpMethod)->setMethodName($methodName);
        return $route;
    }
}
