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
     * @var Magento_Core_Model_Config_Primary
     */
    protected $_primaryConfig;

    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_fileReader;

    /**
     * @param Magento_Core_Model_Config_Primary $primaryConfig
     * @param Magento_Core_Model_Config_Modules_Reader $fileReader
     */
    public function __construct(
        Magento_Core_Model_Config_Primary $primaryConfig,
        Magento_Core_Model_Config_Modules_Reader $fileReader
    ) {
        $this->_primaryConfig = $primaryConfig;
        $this->_fileReader = $fileReader;
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

        $this->_fileReader->loadModulesConfiguration(array('config.xml'), $config);
        Magento_Profiler::stop('load_modules_configuration');

        $config->applyExtends();

        Magento_Profiler::stop('load_modules');
        Magento_Profiler::stop('config');
    }
}
