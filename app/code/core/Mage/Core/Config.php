<?php

class Mage_Core_Config extends Varien_Simplexml_Config
{
    const XPATH_ACTIVE_MODULES = "/config/modules/*[active='true']";

    function __construct()
    {
        parent::__construct();
        
        $this->setCacheDir(Mage::getRoot('var').DS.'cache'.DS.'xml');
        $this->setCacheKey('globalConfig');
        $this->loadGlobal();
    }
    
    function loadGlobal()
    {
        if (true && $xml = $this->loadCache()) {
            $this->setXml($xml);
            return true;
        }
        
        $configFile = Mage::getRoot('etc').DS.'core.xml';
        $this->addCacheStat($configFile);
        $this->setXml($configFile);
        
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
            $moduleConfig = $this->loadFile($configFile);
            $this->_xml->extend($moduleConfig, true);
        }
        return true;
    }
    
    function loadLocal()
    {
        $configFile = Mage::getRoot('etc').DS.'local.xml';
        $this->addCacheStat($configFile);
        $localConfig = $this->loadFile($configFile);
        $this->_xml->extend($localConfig, true);
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
    
    public function loadEventObservers($area)
    {
        $events = $this->getXml()->global->$area->events;
        foreach ($events->children() as $event) {
            $eventName = $event->getName();
            foreach ($event->observers->children() as $observer) {
                $callback = array((string)$observer->class, (string)$observer->method);
                $args = array_values((array)$observer->args);
                Mage::addObserver($eventName, $callback, $args, $observer->getName());
            }
        }
    }
}
