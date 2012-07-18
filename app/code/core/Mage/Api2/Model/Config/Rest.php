<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API REST Config
 *
 * @category Mage
 * @package  Mage_Api2
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Config_Rest extends Magento_Config_XmlAbstract
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
     * Get all modules routes defined in config
     *
     * @return array
     */
    public function getRoutes()
    {
        $routes = array();
        foreach ($this->_data as $route => $routeData) {
            $arguments = array(
                Mage_Api2_Model_Route_Abstract::PARAM_ROUTE    => $route,
                Mage_Api2_Model_Route_Abstract::PARAM_DEFAULTS => $routeData
            );

            $routes[] = new Mage_Api2_Model_Route_Rest($arguments);
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
            /** @var DOMElement $route */
            foreach ($resource->getElementsByTagName('route') as $route) {
                $result[$route->nodeValue] = array(
                    'resource_type' => $route->getAttribute('resource_type'),
                    'controller' => $resource->getElementsByTagName('controller')->item(0)->nodeValue,
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
}