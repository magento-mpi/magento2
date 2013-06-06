<?php
/**
 * DataService config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Config_Reader extends Magento_Config_XmlAbstract
{

    const FILE_NAME = 'service_calls.xml';

    const CONFIG_CACHE_NAME = 'service_calls_config';

    /** @var Mage_Core_Model_Config_Loader_Modules_File  */
    protected $_fileReader;

    /** @var Mage_Core_Model_Cache_Type_Config  */
    protected $_configCacheType;

    /** @var Mage_Core_Model_DataService_Config_Loader  */
    protected $_configLoader;

    /** @var Varien_Simplexml_Config config being cached in memory*/
    protected $_config;

    /**
     * @param Mage_Core_Model_Config_Loader_Modules_File $fileReader
     * @param Mage_Core_Model_Cache_Type_Config $configCacheType
     * @param Mage_Core_Model_DataService_Config_Loader $configLoader
     */
    public function __construct(
        Mage_Core_Model_Config_Loader_Modules_File $fileReader,
        Mage_Core_Model_Cache_Type_Config $configCacheType,
        Mage_Core_Model_DataService_Config_Loader $configLoader
    ) {
        $this->_fileReader = $fileReader;
        $this->_configCacheType = $configCacheType;
        $this->_configLoader = $configLoader;
        $this->_config = null;
    }

    /**
     * Reads all service calls files into one Varien_Simplexml_Config
     *
     * @return Varien_Simplexml_Config
     */
    public function getServiceCallConfig()
    {
        if (is_null($this->_config)) {
            $cachedXml = $this->_configCacheType->load(self::CONFIG_CACHE_NAME);
            if ($cachedXml) {
                $xmlConfig = new Varien_Simplexml_Config($cachedXml);
            } else {
                $xmlConfig = new Varien_Simplexml_Config();
                $xmlConfig->loadString($this->_getServiceCallsCombinedXml());
                $this->_configCacheType->save($xmlConfig->getXmlString(), self::CONFIG_CACHE_NAME);
            }
            $this->_config = $xmlConfig;
        }
        return $this->_config;
    }

    /**
     * Reads all service calls files into one XML string with <service_calls> as the root
     *
     * @return string
     */
    private function _getServiceCallsCombinedXml()
    {
        $sourceFiles = $this->_getServiceCallsFiles();

        return $this->_merge($sourceFiles)->saveXML();
    }

    /**
     * Returns array of files that contain service calls config
     *
     * @return array of files
     */
    private function _getServiceCallsFiles()
    {
        $modulesConfig = $this->_configLoader->getModulesConfig();
        $files = $this->_fileReader
            ->getConfigurationFiles($modulesConfig, self::FILE_NAME);
        return (array)$files;
    }

    /**
     * Returns the schema for the service_calls.xml
     *
     * @return string path to schema file
     */
    public function getSchemaFile()
    {
        return $this->_fileReader->getModuleDir('etc', 'Mage_Core') . DIRECTORY_SEPARATOR . 'service_calls.xsd';
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
     * Get if xml files must be runtime validated
     *
     * Will always return false since we have integrity tests for this, and it would cause problems for our
     * integration testing.
     *
     * @return boolean
     */
    protected function _isRuntimeValidated()
    {
        return false;
    }
}