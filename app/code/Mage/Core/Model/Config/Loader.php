<?php
/**
 * Application config loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Modules configuration object
     *
     * @var Mage_Core_Model_Config_Modules
     */
    protected $_modulesConfig;

    /**
     * Database configuration loader
     *
     * @var Mage_Core_Model_Config_Loader_Db
     */
    protected $_dbLoader;

    /**
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_configFactory;

    /**
     * @param Mage_Core_Model_Config_Modules $config
     * @param Mage_Core_Model_Config_Loader_Db $dbLoader
     * @param Mage_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Model_Config_Loader_Db $dbLoader,
        Mage_Core_Model_Config_BaseFactory $factory
    ) {
        $this->_modulesConfig = $config;
        $this->_dbLoader = $dbLoader;
        $this->_configFactory = $factory;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $config->extend($this->_configFactory->create($this->_modulesConfig->getNode()));
        $this->_dbLoader->load($config);
    }
}
