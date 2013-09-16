<?php
/**
 * Service config data reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Config_Reader extends Magento_Config_XmlAbstract
{
    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return __DIR__ . '/../etc/webapi.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _extractData(DOMDocument $dom)
    {
        return array();
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    protected function _getInitialXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?><config></config>';
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array();
    }

    /**
     * Get Dom configuration model
     * @return Magento_Config_Dom
     */
    protected function _getDomConfigModel()
    {
        if (is_null($this->_domConfig)) {
            $this->_domConfig = new Magento_Webapi_Model_Config_Dom(
                $this->_getInitialXml(),
                $this->_getIdAttributes()
            );
        }
        return $this->_domConfig;
    }

    /**
     * Get if xml files must be runtime validated
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return false;
    }

    /**
     * Retrieve services
     * @return DOMDocument
     */
    public function getServices()
    {
        return $this->_getDomConfigModel()->getDom();
    }
}
