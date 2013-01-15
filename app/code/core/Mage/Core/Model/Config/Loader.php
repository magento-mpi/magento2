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
     * Modules configuration object
     *
     * @var Mage_Core_Model_Config_Locales
     */
    protected $_localesConfig;

    /**
     * Database configuration loader
     *
     * @var Mage_Core_Model_Config_Loader_Db
     */
    protected $_dbLoader;

    /**
     * @param Mage_Core_Model_Config_Modules $config
     * @param Mage_Core_Model_Config_Locales $localesConfig
     * @param Mage_Core_Model_Config_Loader_Db $dbLoader
     */
    public function __construct(
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Model_Config_Locales $localesConfig,
        Mage_Core_Model_Config_Loader_Db $dbLoader
    ) {
        $this->_modulesConfig = $config;
        $this->_localesConfig = $localesConfig;
        $this->_dbLoader = $dbLoader;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $config->extend($this->_modulesConfig);
        $this->_dbLoader->load($config);
        $config->extend($this->_localesConfig);
    }
}
