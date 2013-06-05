<?php
/**
 * Dataservice config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_Config_Reader
{

    const FILE_NAME = 'service_calls.xml';

    const CONFIG_CACHE_NAME = 'service_calls_config';

    const ELEMENT_CLASS = 'Varien_Simplexml_Element';

    /** @var Mage_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;

    /** @var Mage_Core_Model_Cache_Type_Config  */
    protected $_configCacheType;

    /** @var Varien_Simplexml_Config config being cached in memory*/
    protected $_config;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Mage_Core_Model_Cache_Type_Config $configCacheType
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_configCacheType = $configCacheType;
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

        $callsStr = '';
        foreach ($sourceFiles as $filename) {
            $fileStr = file_get_contents($filename);

            /** @var $fileXml Mage_Core_Model_Layout_Element */
            $fileXml = simplexml_load_string($fileStr, self::ELEMENT_CLASS);
            $callsStr .= $fileXml->innerXml();
        }
        return '<?xml version="1.0"?><service_calls>' . $callsStr . '</service_calls>';
    }

    /**
     * Returns array of files that contain service calls config
     *
     * @return array of files
     */
    private function _getServiceCallsFiles()
    {
        $files = $this->_moduleReader
            ->getModuleConfigurationFiles(self::FILE_NAME);
        return (array)$files;
    }

    /**
     * Returns the schema for the service_calls.xml
     */
    public function getSchemaFile()
    {
        return $this->_moduleReader->getModuleDir('etc', 'Mage_Core') . DIRECTORY_SEPARATOR . 'service_calls.xsd';
    }
}