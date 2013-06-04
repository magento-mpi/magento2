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

    const ELEMENT_CLASS = 'Varien_Simplexml_Element';

    /** @var Mage_Core_Model_Config_Modules_Reader  */
    protected $_moduleReader;

    /** @var Mage_Core_Dataservice_Config_Reader */
    protected $_dir;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $moduleReader
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $moduleReader,
        Mage_Core_Model_Dir $dir
    ) {
        $this->_moduleReader = $moduleReader;
        $this->_dir = $dir;
    }

    /**
     * Reads all service calls files into one XML string with <calls> as the root
     *
     * @return string
     */
    public function getServiceCallConfig()
    {
        $sourceFiles = $this->_getServiceCallsFiles();

        $callsStr = '';
        foreach ($sourceFiles as $filename) {
            $fileStr = file_get_contents($filename);

            /** @var $fileXml Mage_Core_Model_Layout_Element */
            $fileXml = simplexml_load_string($fileStr, self::ELEMENT_CLASS);
            $callsStr .= $fileXml->innerXml();
        }
        return '<service_calls>' . $callsStr . '</service_calls>';
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
        return $this->_dir->getDir('app') . '/code/Mage/Core/etc/service_calls.xsd';
    }
}