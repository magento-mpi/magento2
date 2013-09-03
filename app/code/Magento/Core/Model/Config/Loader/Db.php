<?php
/**
 * DB-stored application configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Db implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * Modules configuration
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * DB scheme model
     *
     * @var Magento_Core_Model_Db_UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * Resource model of config data
     *
     * @var Magento_Core_Model_Resource_Config
     */
    protected $_resource;

    /**
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Core_Model_Config_Modules $modulesConfig
     * @param Magento_Core_Model_Resource_Config $resource
     * @param Magento_Core_Model_Db_UpdaterInterface $schemeUpdater
     * @param Magento_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Magento_Core_Model_Config_Modules $modulesConfig,
        Magento_Core_Model_Resource_Config $resource,
        Magento_Core_Model_Db_UpdaterInterface $schemeUpdater,
        Magento_Core_Model_Config_BaseFactory $factory
    ) {
        $this->_config = $modulesConfig;
        $this->_resource = $resource;
        $this->_dbUpdater = $schemeUpdater;
        $this->_configFactory = $factory;
    }

    /**
     * Populate configuration object
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function load(Magento_Core_Model_Config_Base $config)
    {
        if (false == $this->_resource->getReadConnection()) {
            return;
        }

        //update database scheme
         $this->_dbUpdater->updateScheme();

        //apply modules configuration
        $config->extend($this->_configFactory->create($this->_config->getNode()));

        //load db configuration
        \Magento\Profiler::start('load_db');
        $this->_resource->loadToXml($config);
        \Magento\Profiler::stop('load_db');
    }
}
