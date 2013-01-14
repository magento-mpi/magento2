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
     * Locales loader
     *
     * @var Mage_Core_Model_Config_Loader_Locales
     */
    protected $_localesLoader;

    /**
     * @param Mage_Core_Model_Config_Modules $config
     * @param Mage_Core_Model_Config_Loader_Db $dbLoader
     * @param Mage_Core_Model_Config_Loader_Locales $localesLoader
     */
    public function __construct(
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Model_Config_Loader_Db $dbLoader,
        Mage_Core_Model_Config_Loader_Locales $localesLoader
    ) {
        $this->_modulesConfig = $config;
        $this->_dbLoader = $dbLoader;
        $this->_localesLoader = $localesLoader;
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
        $this->_localesLoader->load($config);
    }
}
