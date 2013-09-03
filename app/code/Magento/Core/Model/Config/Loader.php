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
     * Modules configuration object
     *
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_modulesConfig;

    /**
     * Modules configuration object
     *
     * @var Magento_Core_Model_Config_Locales
     */
    protected $_localesConfig;

    /**
     * Database configuration loader
     *
     * @var Magento_Core_Model_Config_Loader_Db
     */
    protected $_dbLoader;

    /**
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_configFactory;

    /**
     * @param Magento_Core_Model_Config_Modules $config
     * @param Magento_Core_Model_Config_Locales $localesConfig
     * @param Magento_Core_Model_Config_Loader_Db $dbLoader
     * @param Magento_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(
        Magento_Core_Model_Config_Modules $config,
        Magento_Core_Model_Config_Locales $localesConfig,
        Magento_Core_Model_Config_Loader_Db $dbLoader,
        Magento_Core_Model_Config_BaseFactory $factory
    ) {
        $this->_modulesConfig = $config;
        $this->_localesConfig = $localesConfig;
        $this->_dbLoader = $dbLoader;
        $this->_configFactory = $factory;
    }

    /**
     * Populate configuration object
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function load(Magento_Core_Model_Config_Base $config)
    {
        $config->extend($this->_configFactory->create($this->_modulesConfig->getNode()));
        $this->_dbLoader->load($config);
        $config->extend($this->_configFactory->create($this->_localesConfig->getNode()));
    }
}
