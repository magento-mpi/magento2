<?php

/**
 * Core configuration class
 * 
 * Used to retrieve core configuration values
 *
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Core_Config extends Varien_Simplexml_Config
{
    /**
     * Xpath for active modules. Used during compilation of cache config file
     *
     */
    const XPATH_ACTIVE_MODULES = "/config/modules/*[active='true']";

    /**
     * Constructor
     *
     */
    function __construct()
    {
    	parent::__construct();
    }

    /**
     * Initialization of core config
     *
     */
    function init()
    {
    	$this->setCacheDir(Mage::getBaseDir('var').DS.'cache'.DS.'config');
    	$this->setCacheKey('globalConfig');

    	$this->loadGlobal();
    }


    /**
     * Config load sequence. Executed only in case of missing cache
     *
     * @return boolean
     */
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

    /**
     * Load core config from /app/etc/core.xml
     *
     * @return boolean
     */
    function loadCore()
    {
        $configFile = Mage::getBaseDir('etc').DS.'core.xml';
        $this->addCacheStat($configFile);
        $this->setXml($configFile);

        return true;
    }

    /**
     * Load modules config for active modules from /app/code/<pool>/<module>/etc/config.xml
     *
     * Overwrites core config
     *
     * @return boolean
     */
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

    /**
     * Load local config from /app/etc/local.xml
     * 
     * Usually contains db connections configurations.
     * Overwrites core and modules configs.
     *
     * @return boolean
     */
    function loadLocal()
    {
        $configFile = Mage::getBaseDir('etc').DS.'local.xml';
        $this->addCacheStat($configFile);
        $localConfig = $this->loadFile($configFile);
        $this->_xml->extend($localConfig, true);
        return true;
    }
    
    /**
     * Load configuration values from database.
     * 
     * Overwrites all other configs
     *
     */
    function loadFromDb()
    {
        /*
        $coreModel = (string)$this->_xml->modules->Mage_Core->load->all->models->core->class;
        $className = $coreModel.'_Config';
        $model = new $className();
        */
        $model = $this->getResourceModelClass('core', 'config');
        $model->updateXmlFromDb($this->_xml);
    }

    /**
     * Remove extra information from cached config to improve performance.
     * 
     * not used
     *
     * @return boolean
     */
    function removeExtraSource()
    {
        $modules = $this->getXpath(self::XPATH_ACTIVE_MODULES);
        if (!$modules) {
            return false;
        }
        foreach ($modules as $module) {
            unset($module->load);
        }
        return true;
    }
    
    /**
     * General function to retrieve collection or item from global configuration
     * 
     * @param	string $collection
     * @param 	string $name
     * @return	array|Varien_Simplexml_Object
     */
    function getGlobalConfig($collection, $name='')
    {
        $config = Mage::getConfig()->getXml()->global->$collection;
        if (''===$name) {
            $arr = array();
            foreach ($config as $node) {
                if ($node->active) {
                    $arr[$node->getName()] = $node;
                }
            }
            return $arr;
        }
        if (!isset($config->$name)) {
            return false;
        }
        return $config->$name;
    }
    
    /**
     * Get instance of class if available from global collection
     * 
     * @param string $collection
     * @param string $name
     * @return object
     */
    function getGlobalInstance($collection, $name)
    {
        $x = $this->getGlobalConfig($collection, $name);
        if (!$x) {
            return false;
        }
        $className = (string)$x->class;
        return new $className();
    }
    
    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Object
     */
    function getModule($moduleName='')
    {
        if (''===$moduleName) {
            return $this->_xml->modules;
        } else {
            return $this->_xml->modules->$moduleName;
        }
    }
    
    /**
     * Get module setup class instance.
     * 
     * Defaults to Mage_Core_Setup
     *
     * @param string|Varien_Simplexml_Object $module
     * @return object
     */
    function getModuleSetup($module='')
    {
        $className = 'Mage_Core_Setup';
        if (''!==$module) {
            if (is_string($module)) {
                $module = $this->getModule($module);
            }
	        if (isset($module->setup) && isset($module->setup->class)) {
	            $className = $module->setup->class;
	        }
        }
        return new $className($module);
    }
    
    /**
     * Get base filesystem directory. depends on $type
     * 
     * If $moduleName is specified retrieves specific value for the module.
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
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
    
    /**
     * Get base URL path. depends on $type
     * 
     * If $moduleName is specified retrieves specific value for the module.
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
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
            $url = Mage::registry('controller')->getRequest()->getBaseAppUrl();

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
    
    /**
     * Load event observers for an area (front, admin)
     *
     * @param string $area
     * @return boolean
     */
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

    /**
     * Get standard path variables.
     * 
     * To be used in blocks, templates, etc.
     *
     * @param array|string $args Module name if string
     * @return array
     */
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
    
    /**
     * Load step for an area.
     * 
     * Goes over installed modules and checks for existance of class for the $areaName.
     * For example: Mage_Core_Module_Front
     * After that checks for existance of $methodName and if exists - executes.
     *
     * @param string $areaName
     * @param string $methodName
     */
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

    /**
     * Get model class instance.
     * 
     * Example:
     * $config->getResourceModelInstance('catalog', 'product')
     * 
     * Will instantiate Mage_Catalog_Resource_Model_Mysql4_Product
     *
     * @param string $model
     * @param string $class
     * @param array|object $constructArguments
     * @return Mage_Core_Resource_Model_Abstract
     */
    public function getResourceModelInstance($model, $class='', $constructArguments=array())
    {
        $className = '';
        if ($xml = $this->getXml()) {
            $className = (string)$xml->global->resourceModels->$model->class;
        }     

        if (''!==$class) {
            $className .= '_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($class))));
        }

        return new $className($constructArguments);
    }
    
    /**
     * Get resource configuration for resource name
     *
     * @param string $name
     * @return Varien_Simplexml_Object
     */
    public function getResourceConfig($name)
    {
        return $this->getXml()->global->resources->$name;
    }
    
    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        $conn = $config->connection;
        if (!empty($conn['use'])) {
            return $this->getResourceConnectionConfig((string)$conn['use']);
        } else {
            return $conn;
        }
    }
       
    /**
     * Retrieve resource type configuration for resource name
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getResourceType($type)
    {
        return $this->getXml()->global->resourceConnectionTypes->$type;
    }
    
     /**
     * Get block type(s) from config
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getBlockType($type='')
    {
        $types = $this->getXml()->global->blockTypes;
        if (''===$type) {
            return $types;
        } else {
            return $types->$type;
        }
    }
    
}
