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
        $this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'config');
        $this->setCacheKey('globalConfig');
        
        $this->loadGlobal();
    }
    
    function loadGlobal()
    {
        if ($xml = $this->loadCache()) {
            $this->setXml($xml);
            return true;
        }

        $this->loadCore();
        $this->loadModules();
        $this->loadLocal();
        
        $this->applyExtends();
        $this->applyExtends();
        $this->applyExtends();

        $this->loadFromDb();

        #$this->removeExtraSource();

        $this->saveCache();

        return true;
    }

    function loadCore()
    {
        $configFile = Mage::getBaseDir('etc').DS.'core.xml';
        $this->addCacheStat($configFile);
        $this->setXml($configFile);

        return true;
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
        return true;
    }
    
    function loadFromDb()
    {
        /*
        $coreModel = (string)$this->_xml->modules->Mage_Core->load->all->models->core->class;
        $className = $coreModel.'_Config';
        $model = new $className();
        */
        $model = $this->getModelClass('core', 'config');
        $model->updateXmlFromDb($this->_xml);
    }

    function removeExtraSource()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        if (!$modules) {
            return false;
        }
        foreach ($modules as $module) {
            unset($module->load);
        }
    }
    
    function getModule($moduleName='')
    {
        if (''===$moduleName) {
            return $this->_xml->modules;
        } else {
            return $this->_xml->modules->$moduleName;
        }
    }
    
    function getModuleSetup($module)
    {
        if (isset($module->setup) && isset($module->setup->class)) {
            $className = $module->setup->class;
        } else {
            $className = 'Mage_Core_Setup';
        }
        return new $className($module);
    }
    
    function getBaseDir($type='', $moduleName='')
    {
        if (''!==$moduleName) {
            $module = self::getModule($moduleName);
            $modulePath = str_replace(' ', DS, ucwords(str_replace('_', ' ', $moduleName)));
            $dir = Mage::getBaseDir('code').DS.$module->codePool.DS.$modulePath;
            
            switch ($type) {
                case 'layout':
                    $dir = $this->getBaseDir('layout').DS.$modulePath;
                    break;
                    
                case 'views':
                    //$dir .= DS.'views';
                    $dir = $this->getBaseDir('layout').DS.$modulePath.DS.'views';
                    break;

                case 'etc':
                    $dir .= DS.'etc';
                    break;
                    
                case 'controllers':
                    $dir .= DS.'controllers';
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
                    } else {
                        $url .= '/'.strtolower($moduleName);
                    }
                    break;
            }
        } else {
            $url = Mage::registry('controller')->getRequest()->getBaseUrl();

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
    
    public function applyDbUpdates()
    {
        $modules = $this->getModule()->children();
        foreach ($modules as $module) {
            $setupClass = $this->getModuleSetup($module);
            $setupClass->applyDbUpdates();
        }
        return true;
    }
    
    public function loadEventObservers($area)
    {
        $events = $this->getXml()->global->$area->events->children();
        foreach ($events as $event) {
            $eventName = $event->getName();
            $observers = $event->observers->children();
            foreach ($observers as $observer) {
                $callback = array((string)$observer->class, (string)$observer->method);
                #$args = array_values((array)$observer->args);
                $args = array($observer->args);
                Mage::addObserver($eventName, $callback, $args, $observer->getName());
            }
        }
        return true;
    }
    
    public function getPathVars($args)
    {
        $path = array();
        
        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSkinUrl'] = Mage::getBaseUrl('skin');
        
        if(is_array($args)) {
            $moduleName = isset($args['moduleName']) ? $args['moduleName'] : '';
        }
        else {
            $moduleName = $args;
        }
        
        if (!empty($moduleName)) {
            $path['baseModuleUrl'] = $this->getBaseUrl('', $moduleName);
        } else {
            $path['baseModuleUrl'] = '';
        }
        $path['baseJsUrl'] = Mage::getBaseUrl('js');
        
        return $path;
    }
    
    public function loadStep($areaName, $methodName) 
    {
        $this->loadStep('all', $methodName);
        
        $modules = $this->getXml()->modules;
        foreach ($modules as $module) {
            $area = $module->load->$areaName;
            if (empty($area)) {
                continue;
            }
            $load = $area->useModuleSteps;
            if (empty($load) || 'true'!==(string)$load) {
                continue;
            }
            $callback = array($moduleName.'_Module_'.ucwords(strtolower($areaName)), $methodName);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    public function getModelClass($model, $class='', $constructArguments=array())
    {
        $className = '';
        if ($xml = $this->getXml()) {
            $className = (string)$xml->global->models->$model->class;
        }     

        if (''!==$class) {
            $className .= '_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
        }

        return new $className($constructArguments);
    }
    
    /**
     * Retrieve named resource
     *
     * @param string $name
     * @return resource || false
     */
    public function getResource($name='')
    {
        if (!Mage::registry('resources')) {
            Mage::register('resources', array());
        }
        $resources = Mage::registry('resources');
        
        if ($name=='') {
            return $resources;
        }
        
        if (!Mage::registry($name)) {
            $global = $this->getXml()->global;
            $resource = $global->resources->$name;
            $rType = (string)$resource->connection->type;
            $rTypeClass = (string)$global->resourceTypes->$rType->class;
            $resources[$name] = new $rTypeClass($resource);
            if (!isset($resources[$name])) {
                Mage::exception('Non existing resource requested: '.$name);
            }
            Mage::register('resources', $resources);
        }
        
        return $resources[$name];
    }
    
    public function getResourceEntity($resource, $entity='')
    {
        $entities = $this->getXml()->global->resources->$resource->entities;
        if (''===$entity) {
            return $entities;
        } else {
            return $entities->$entity;
        }
    }
}
