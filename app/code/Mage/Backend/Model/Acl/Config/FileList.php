<?php
/**
 * ACL Resource configuration file list
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Backend_Model_Acl_Config_FileList
    implements Magento_Acl_Loader_Resource_ConfigReader_FileListInterface
{
    /**
     * Module file reader
     *
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     */
    public function __construct(Magento_Core_Model_Config_Modules_Reader $moduleReader)
    {
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve list of configuration files
     *
     * @return array
     */
    public function asArray()
    {
        return (array) $this->_moduleReader->getModuleConfigurationFiles('adminhtml' . DIRECTORY_SEPARATOR . 'acl.xml');
    }
}
