<?php
/**
 * Application config loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * Primary application configuration
     *
     * @var Mage_Core_Model_Config_Primary
     */
    protected $_primaryConfig;

    /**
     * @var Mage_Core_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * @var Mage_Core_Model_Config_Modules_Reader
     */
    protected $_fileReader;

    /**
     * @var Mage_Core_Model_Config_Loader_Local
     */
    protected $_localLoader;

    /**
     * @param Mage_Core_Model_Config_Primary $primaryConfig
     * @param Mage_Core_Model_Config_Resource $resourceConfig
     * @param Mage_Core_Model_Config_Modules_Reader $fileReader
     * @param Mage_Core_Model_Config_Loader_Local $localLoader
     */
    public function __construct(
        Mage_Core_Model_Config_Primary $primaryConfig,
        Mage_Core_Model_Config_Resource $resourceConfig,
        Mage_Core_Model_Config_Modules_Reader $fileReader,
        Mage_Core_Model_Config_Loader_Local $localLoader
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_fileReader = $fileReader;
        $this->_localLoader = $localLoader;
    }

    /**
     * Populate configuration object
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function load(Magento_Core_Model_Config_Base $config)
    {
        if (!$config->getNode()) {
            $config->loadString('<config></config>');
        }

        Magento_Profiler::start('config');
        Magento_Profiler::start('load_modules');

        $config->extend($this->_primaryConfig);

        Magento_Profiler::start('load_modules_configuration');

        $resourceConfig = sprintf('config.%s.xml', $this->_resourceConfig->getResourceConnectionModel('core'));
        $this->_fileReader->loadModulesConfiguration(array('config.xml', $resourceConfig), $config);
        Magento_Profiler::stop('load_modules_configuration');

        // Prevent local configuration overriding
        $this->_localLoader->load($config);

        $config->applyExtends();

        Magento_Profiler::stop('load_modules');
        Magento_Profiler::stop('config');
    }
}
