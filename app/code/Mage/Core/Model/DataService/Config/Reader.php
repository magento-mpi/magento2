<?php
/**
 * Magento Data Service Config reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Config_Reader extends Magento_Config_XmlAbstract
{
    /**
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    private $_modulesReader;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $modulesReader
     * @param array $configFiles
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $modulesReader,
        array $configFiles
    ) {
        if (count($configFiles)) {
            parent::__construct($configFiles);
        }
        $this->_modulesReader = $modulesReader;
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    public function getSchemaFile()
    {
        return $this->_modulesReader->getModuleDir('etc', 'Mage_Core') . '/service_calls.xsd';
    }

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
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
        return '<?xml version="1.0"?><service_calls></service_calls>';
    }

    /**
     * Get if xml files must be runtime validated
     *
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return false;
    }

    /**
     * Retrieve Service Calls
     *
     * @return DOMDocument
     */
    public function getServiceCallConfig()
    {
        return $this->_getDomConfigModel()->getDom();
    }

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    protected function _getIdAttributes()
    {
        return array(
            '/service_calls/service_call/arg' => 'name',
            '/service_calls/service_call' => 'name',
        );
    }

    /**
     * Perform xml validation
     *
     * @return Magento_Config_XmlAbstract
     * @throws Magento_Exception if invalid XML-file passed
     */
    public function validate()
    {
        return $this->_performValidate();
    }
}