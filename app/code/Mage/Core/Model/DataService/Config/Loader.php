<?php
/**
 * Config loader that exposes modules config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_Config_Loader extends Mage_Core_Model_Config_Loader_Modules
{

    /**
     * Returns sorted modules config
     *
     * @return Mage_Core_Model_Config_Base
     */
    public function getModulesConfig()
    {
        $config = new Mage_Core_Model_Config_Base('<config><modules></modules></config>');

        $this->_loadDeclaredModules($config);

        return $config;
    }

    public function getServiceCallConfig()
    {
        return $this->load(new Mage_Core_Model_Config_Base('<config><modules></modules></config>'));
    }

    public function load(Mage_Core_Model_Config_Base $config)
    {
        $this->_loadDeclaredModules($config);

        $serviceCalls = new Mage_Core_Model_Config_Base('<service_calls />');

        $this->_fileReader->loadConfigurationFromFile(
            $config, 'service_calls.xml', $serviceCalls, null, $this->_modulesCache
        );


        return $serviceCalls;
    }
}