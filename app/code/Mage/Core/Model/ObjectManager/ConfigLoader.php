<?php
/**
 * ObjectManager configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager_ConfigLoader
{
    /**
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_modulesReader;

    /**
     * @var Magento_ObjectManager_Config_Reader_DomFactory
     */
    protected $_readerFactory;

    /**
     * @param Mage_Core_Model_Config_Modules_Reader $modulesReader
     * @param Magento_ObjectManager_Config_Reader_DomFactory $readerFactory
     */
    public function __construct(
        Mage_Core_Model_Config_Modules_Reader $modulesReader,
        Magento_ObjectManager_Config_Reader_DomFactory $readerFactory
    ) {
        $this->_modulesReader = $modulesReader;
        $this->_readerFactory = $readerFactory;
    }

    /**
     * Load modules DI configuration
     *
     * @param string $area
     * @return array
     */
    public function load($area)
    {
        $fileName = 'global' == $area ? 'di.xml' : $area . DIRECTORY_SEPARATOR . 'di.xml';
        $configFiles = $this->_modulesReader->getModuleConfigurationFiles($fileName);
        /** @var Magento_ObjectManager_Config_Reader_Dom $reader */
        $reader = $this->_readerFactory->create(array('configFiles' => $configFiles));
        return $reader->read();
    }
}
