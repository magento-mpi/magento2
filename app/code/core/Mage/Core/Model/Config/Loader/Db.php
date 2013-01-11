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
    protected $_config;
    protected $_localConfig;

    public function __construct(Mage_Core_Model_Config_Local $localConfig, Mage_Core_Model_Config_Modules $config)
    {
        $this->_config = $config;
        $this->_localConfig = $localConfig;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config) //$config is empty
    {
        //load db data
        $config->extend($this->_config);
        $config->extend($data);
        $config->extend($this->_localConfig);
    }

}
