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
     * Configuration loader factory
     *
     * @var Mage_Core_Model_Config_LoaderFactory
     */
    protected $_loaderFactory;

    /**
     * Loader names
     *
     * @var array
     */
    protected $_loaders;

    /**
     * @param Mage_Core_Model_Config_LoaderFactory $loaderFactory
     * @param array $loaders
     */
    public function __construct(
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Model_Config_Loader_Db $loaderDb,
        Mage_Core_Model_Config_Loader_Local $loaderLocale

    ) {

    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $config->extend($this->_config);
        $loaderDb->load($config);
        $loaderLocale->load($config);
    }
}
