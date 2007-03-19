<?php

class Mage_Core_Config extends Varien_Simplexml_Config
{
    const XPATH_ACTIVE_MODULES = "/config/modules/*[active='true']";

    function __construct()
    {
        parent::__construct();
    }
    
    function init()
    {
        $this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'xml');
        $this->setCacheKey('globalConfig');
        $this->loadGlobal();
    }
    
    function loadGlobal()
    {
        if (true && $xml = $this->loadCache()) {
            $this->setXml($xml);
            return true;
        }

        $this->loadCore();
        $this->loadModules();
        $this->loadLocal();
        
        $this->applyExtends();
        $this->applyExtends();
        
        $this->saveCache();
        
        return true;
    }
    
    function loadCore()
    {
        $configFile = Mage::getBaseDir('etc').DS.'core.xml';
        $this->addCacheStat($configFile);
        $this->setXml($configFile);
    }
    
    function loadModules()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        if (!$modules) {
            return false;
        }
        foreach ($modules as $module) {
            $configFile = Mage::getBaseDir('code').DS.$module->codePool.DS.str_replace('_',DS,$module->getName()).DS.'etc'.DS.'config.xml';
            $this->addCacheStat($configFile);
            $moduleConfig = $this->loadFile($configFile);
            $this->_xml->extend($moduleConfig, true);
        }
        return true;
    }
    
    function loadLocal()
    {
        $configFile = Mage::getBaseDir('etc').DS.'local.xml';
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
    
    function getBaseDir($type='', $moduleName='')
    {
        if (''!==$moduleName) {
            $module = self::getModule($moduleName);
            $modulePath = str_replace(' ', DS, ucwords(str_replace('_', ' ', $moduleName)));
            $dir = Mage::getBaseDir('code').DS.$module->codePool.DS.$modulePath;
            
            switch ($type) {
                case 'etc':
                    $dir .= DS.'etc';
                    break;
                    
                case 'controllers':
                    $dir .= DS.'controllers';
                    break;
                    
                case 'views':
                    //$dir .= DS.'views';
                    $dir = $this->getBaseDir('layout').DS.$modulePath.DS.'views';
                    break;
                    
                case 'sql':
                    $dir .= DS.'sql';
                    break;
            }
        } else {
            $dir = Mage::getRoot();
            switch ($type) {
                case 'etc':
                    $dir .= DS.'etc';
                    break;
                case 'code':
                    $dir .= DS.'code';
                    break;
                case 'layout':
                    $dir .= DS.'layout';
                    break;
                case 'var':
                    $dir = dirname($dir).DS.'var';
                    break;
            }
        }

        return $dir;
    }
    
    function getBaseUrl($type='', $moduleName='')
    {
        if (''!==$moduleName) {
            $module = $this->getModule($moduleName);
            $url = $this->getBaseUrl($type);
            
            switch ($type) {
                  
                default:
                    if (isset($module->load->front->controller->frontName)) {
                        $url .= '/'.$module->load->front->controller->frontName;
                    }
                    break;
            }
        } else {
            $url = Mage_Core_Controller::getController()->getRequest()->getBaseUrl();

            switch ($type) {
                case 'skin':
                    $url .= '/skins/default';
                    break;
    
                case 'js':
                    //$url = preg_replace('#/admin$#', '', $url).'/js';
                    $url .= '/js';
                    break;                
            }
        }
        
        return $url;
    }
    
    public function checkModulesDbChanges()
    {
        $modules = $this->getModule()->children();
        foreach ($modules as $module) {
            $setupClassName = $module->getName().'_Setup';
            $setupClass = new $setupClassName($module);
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
    
    public function getPathVars($args)
    {
        $path = array();
        
        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSkinUrl'] = Mage::getBaseUrl('skin');
        if (isset($args['moduleName'])) {
            $path['baseModuleUrl'] = $this->getBaseUrl('', $args['moduleName']);
        } else {
            $path['baseModuleUrl'] = '';
        }
        $path['baseJsUrl'] = Mage::getBaseUrl('js');
        
        return $path;
    }
}
