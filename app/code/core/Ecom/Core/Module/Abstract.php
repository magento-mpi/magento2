<?php

abstract class Ecom_Core_Module_Abstract
{

    /**
     * Module information
     *
     * [version, name]
     *
     * @var array
     */
    protected $_info = null;
    protected $_setup = null;

    public function __construct($loadConfig=true)
    {
        if ($loadConfig) {
            $this->loadConfig();
        }
        
        $this->load();
    }

    /**
     * Get module information
     *
     * @return Ecom_Core_Module_Info
     */
    public function getModuleInfo()
    {
        return Ecom::getModuleInfo($this->getInfo('name'));
    }

    public function getInfo($key='')
    {
        if (''===$key) {
            return $this->_info;
        } else {
            return $this->_info[$key];
        }
    }
    
    public function loadConfig()
    {
        $this->_addConfigSections();

        $moduleInfo = $this->getModuleInfo();
        
        $moduleInfo->loadConfig('load');
        $moduleInfo->loadConfig('*user*');
        $moduleInfo->processConfig();
        
        $moduleInfo->checkDepends();
        
        Ecom::getController()->loadModule($moduleInfo);
    }
    
    public function load()
    {
        
    }

    protected function _addConfigSections()
    {
        
    }

}