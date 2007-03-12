<?php

class Mage_Core_Config extends Mage_Core_Config_Xml
{
    const XPATH_ACTIVE_MODULES = "/config/modules/module[active='true']";
    
    const XPATH_EVENT_OBSERVERS = "/config/modules/module/area[@name='all']/events";
    const XPATH_RESOURCE_TYPES = "/config/modules/module/area[@name='all']/resourceTypes";
    const XPATH_RESOURCES = "/config/modules/module/area[@name='all']/resources";
    const XPATH_MODELS = "/config/modules/module/area[@name='all']/models";
    const XPATH_BLOCK_TYPES = "/config/modules/module/area[@name='all']/blocks";

    function __construct()
    {
        parent::__construct();
        
        if (true && $xml = $this->cacheLoad('globalConfig')) {
            $this->load('xml', $xml);
        } else {
            $this->compileGlobalConfig();
        }
    }
    
    function compileGlobalConfig()
    {
        $configFile = Mage::getRoot('etc').DS.'core.xml';
        $this->load('file', $configFile);
        
        $this->loadModules();
        $this->loadLocal();
        $this->applyExtends();
        
        $this->cacheSave('globalConfig');
    }
    
    function loadModules()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        if (!$modules) {
            return false;
        }
        
        foreach ($modules as $module) {
            $configFile = Mage::getModuleInfo($module['name'])->getRoot('etc').DS.'config.xml';
            $moduleConfig = new Mage_Core_Config_Xml('file', $configFile);
            $this->_xml->extend($moduleConfig->getXml(), true);
        }

        return true;
    }
    
    function loadLocal()
    {
        $configFile = Mage::getRoot('etc').DS.'local.xml';
        $localConfig = new Mage_Core_Config_Xml('file', $configFile);
        $this->_xml->extend($localConfig->getXml(), true);
    }

}