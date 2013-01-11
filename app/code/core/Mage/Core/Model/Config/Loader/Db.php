<?php
/**
 * DB-stored application configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Db implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Modules configuration
     *
     * @var Mage_Core_Model_Config_Modules
     */
    protected $_config;

    /**
     * DB scheme model
     *
     * @var Mage_Core_Model_Db_UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * Resource model of config data
     *
     * @var Mage_Core_Model_Resource_Config
     */
    protected $_resource;

    public function __construct(
        Mage_Core_Model_Config_Modules $modulesConfig,
        Mage_Core_Model_Resource_Config $resource,
        Mage_Core_Model_Db_UpdaterInterface $schemeUpdater
    ) {
        $this->_config = $modulesConfig;
        $this->_resource = $resource;
        $this->_dbUpdater = $schemeUpdater;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        //update database scheme
        $this->_dbUpdater->updateScheme();

        //apply modules configuration
        $config->extend($this->_config);

        //load db configuration
        Magento_Profiler::start('load_db');
        $this->_resource->loadToXml($config);
        Magento_Profiler::stop('load_db');
    }
}
