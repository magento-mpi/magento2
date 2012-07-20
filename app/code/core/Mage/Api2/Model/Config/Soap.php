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
 * API SOAP Config
 *
 * @category Mage
 * @package  Mage_Api2
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Config_Soap extends Magento_Config_XmlAbstract
{
    /**
     * Get absolute path to soap.xsd
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/soap.xsd';
    }

    /**
     * Get all controllers defined for each resource from config
     *
     * @return array
     */
    public function getControllers()
    {
        return $this->_data;
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    protected function _extractData(DOMDocument $dom)
    {
        $result = array();

        /** @var DOMElement $resource */
        foreach ($dom->getElementsByTagName('resource') as $resource) {
            $controllerName = $resource->getElementsByTagName('controller')->item(0)->nodeValue;
            $result[$resource->getAttribute('name')] = $controllerName;
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
