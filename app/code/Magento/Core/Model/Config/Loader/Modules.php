<?php
/**
 * Module configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Modules implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * Primary application configuration
     *
     * @var Magento_Core_Model_Config_Primary
     */
    protected $_primaryConfig;

    /**
     * @var Magento_Core_Model_Config_Resource
     */
    protected $_resourceConfig;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_fileReader;

    /**
     * @var Magento_Core_Model_Config_Loader_Local
     */
    protected $_localLoader;

    /**
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param Magento_Core_Model_Config_Resource $resourceConfig
     * @param Magento_Core_Model_Config_Modules_Reader $fileReader
     * @param Magento_Core_Model_Config_Loader_Local $localLoader
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $primaryConfig,
        Magento_Core_Model_Config_Resource $resourceConfig,
        Magento_Core_Model_Config_Modules_Reader $fileReader,
        Magento_Core_Model_Config_Loader_Local $localLoader
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

        \Magento\Profiler::start('config');
        \Magento\Profiler::start('load_modules');

        $config->extend($this->_primaryConfig);

        \Magento\Profiler::start('load_modules_configuration');

        $resourceConfig = sprintf('config.%s.xml', $this->_resourceConfig->getResourceConnectionModel('core'));
        $this->_fileReader->loadModulesConfiguration(array('config.xml', $resourceConfig), $config);
        \Magento\Profiler::stop('load_modules_configuration');

        // Prevent local configuration overriding
        $this->_localLoader->load($config);

        $config->applyExtends();

        \Magento\Profiler::stop('load_modules');
        \Magento\Profiler::stop('config');
        $this->_resourceConfig->setConfig($config);
    }
}
