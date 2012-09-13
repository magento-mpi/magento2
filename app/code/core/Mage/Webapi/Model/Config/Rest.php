<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API REST Config
 *
 * @category Mage
 * @package  Mage_Webapi
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Config_Rest extends Magento_Config_XmlAbstract
{
    /**
     * Get absolute path to validation.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/rest.xsd';
    }

    /**
     * Retrieve controller class name for given resource.
     *
     * @param string $resourceName
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getControllerClassByResourceName($resourceName)
    {
        if (!isset($this->_data[$resourceName])) {
            throw new InvalidArgumentException(sprintf('Resource "%s" not found in config.', $resourceName));
        }
        return $this->_data[$resourceName]['controller'];
    }

    /**
     * Get all modules routes defined in config
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        $apiTypeRoutePath = str_replace(':api_type', 'rest', Mage_Webapi_Controller_Router_Route_ApiType::API_ROUTE);

        foreach ($this->_data as $resourceName => $resourceData) {
            foreach ($resourceData['routes'] as $routeData) {
                $route = new Mage_Webapi_Controller_Router_Route_Rest($apiTypeRoutePath . $routeData['path']);
                $route->setResourceName($resourceName);
                $route->setResourceType($routeData['resource_type']);
                $routes[] =$route;
            }
        }

        return $routes;
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @throws Magento_Exception
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        $result = array();

        /** @var DOMElement $resource */
        foreach ($dom->getElementsByTagName('resource') as $resource) {
            $resourceName = $resource->getAttribute('name');
            $result[$resourceName]['controller'] = $resource->getElementsByTagName('controller')->item(0)->nodeValue;
            /** @var DOMElement $route */
            foreach ($resource->getElementsByTagName('route') as $route) {
                $result[$resourceName]['routes'][] = array(
                    'resource_type' => $route->getAttribute('resource_type'),
                    'path' => $route->nodeValue,
                );
            }
        }

        return $result;
    }

    /**
     * Get initial XML of a valid document
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="UTF-8"?><config></config>';
    }

    /**
     * Define id attributes for entities
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/config/resource' => 'name',
        );
    }

    /**
     * Identify route path by resource name and route type (item/collection)
     *
     * @param string $resourceName
     * @param string $resourceType
     * @return string
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function getRouteByResource($resourceName, $resourceType)
    {
        if (!isset($this->_data[$resourceName])) {
            throw new InvalidArgumentException("Resource '%s' not found.", $resourceName);
        }
        foreach ($this->_data[$resourceName]['routes'] as $routeData) {
            if ($routeData['resource_type'] == $resourceType) {
                return (string)$routeData['path'];
            }
        }
        throw new RuntimeException("Route not found.");
    }
}
