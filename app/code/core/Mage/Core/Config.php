<?php

class Mage_Core_Config extends Mage_Core_Config_Xml
{
    protected $_xml;
    
    const XPATH_ACTIVE_MODULES = "/config/modules/*[active='true']";

    function __construct()
    {
        parent::__construct();
        
        $this->setCacheKey('globalConfig');
        $this->loadGlobal();
    }
    
    function loadGlobal()
    {
        if (true && $xml = $this->loadCache()) {
            $this->load('xml', $xml);
            return true;
        }
        
        $configFile = Mage::getRoot('etc').DS.'core.xml';
        $this->addCacheStat($configFile);
        $this->load('file', $configFile);
        
        $this->loadModules();
        $this->loadLocal();
        $this->applyExtends();
        $this->applyExtends();
        
        $this->saveCache();
        
        return true;
    }
    
    function loadModules()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        if (!$modules) {
            return false;
        }
        foreach ($modules as $module) {
            $configFile = Mage::getRoot('code').DS.$module->codePool.DS.str_replace('_',DS,$module->getName()).DS.'etc'.DS.'config.xml';
            $this->addCacheStat($configFile);
            $moduleConfig = new Mage_Core_Config_Xml('file', $configFile);
            $this->_xml->extend($moduleConfig->getXml(), true);
        }
        return true;
    }
    
    function loadLocal()
    {
        $configFile = Mage::getRoot('etc').DS.'local.xml';
        $localConfig = new Mage_Core_Config_Xml('file', $configFile);
        $this->addCacheStat($configFile);
        $this->_xml->extend($localConfig->getXml(), true);
    }
    
    function getModule($moduleName='')
    {
        if (''===$moduleName) {
            return $this->_xml->modules;
        } else {
            return $this->_xml->modules->$moduleName;
        }
    }
    
    function getModuleRoot($moduleName, $type='')
    {
        $module = self::getModule($moduleName);
        $modulePath = str_replace(' ', DS, ucwords(str_replace('_', ' ', $moduleName)));
        $dir = Mage::getRoot('code').DS.$module->codePool.DS.$modulePath;
        
        switch ($type) {
            case 'etc':
                $dir .= DS.'etc';
                break;
                
            case 'controllers':
                $dir .= DS.'controllers';
                break;
                
            case 'views':
                //$dir .= DS.'views';
                $dir = Mage::getRoot('layout').DS.$modulePath.DS.'views';
                break;
                
            case 'sql':
                $dir .= DS.'sql';
                break;
        }
        return $dir;
    }
    
    function getModuleBaseUrl($moduleName, $type='')
    {
        $module = $this->getModule($moduleName);
        $url = Mage::getBaseUrl($type);
        
        switch ($type) {
              
            default:
                if (isset($module->load->front->controller->frontName)) {
                    $url .= '/'.$module->load->front->controller->frontName;
                }
                break;
        }
        return $url;
    }
    
    public function checkModulesDbChanges()
    {
        $modules = $this->getModule()->children();
        foreach ($modules as $module) {
            $setupClassName = $module->getName().'_Setup';
            $setupClass = new $setupClassName($module, $this);
            $setupClass->applyDbUpdates();
        }
    }
}
