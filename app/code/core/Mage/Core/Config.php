<?php

/**
 * Core configuration class
 *
 * Used to retrieve core configuration values
 *
 * @copyright   2007 Varien Inc.
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @package     Mage
 * @subpackage  Core
 * @link        http://var-dev.varien.com/wiki/doku.php?id=magento:api:mage:core:config
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Core_Config extends Varien_Simplexml_Config
{
    /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->_elementClass = 'Mage_Core_Config_Element';
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

        $this->loadFromDb();

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
        $modules = $this->getNode('modules')->children();
        if (!$modules) {
            return false;
        }
        foreach ($modules as $module) {
            if (!$module->is('active')) {
                continue;
            }
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
        if (!is_file($configFile)) {
            die('File ' . $configFile . ' not found. Copy it from ' . $configFile . '.dev');
        }
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
        try{
            Mage::getModel('core_resource', 'config')->updateXmlFromDb($this->_xml);
        }
        catch (Exception $e) {

        }
    }

    /**
     * Get module config node
     *
     * @param string $moduleName
     * @return Varien_Simplexml_Object
     */
    function getModule($moduleName='')
    {
        $modules = $this->getNode('modules');
        if (''===$moduleName) {
            return $modules;
        } else {
            return $modules->$moduleName;
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
                $className = $module->setup->getClassName();
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
            $modulePath = uc_words($moduleName, DS);
            $dir = Mage::getBaseDir('code').DS.$module->codePool.DS.$modulePath;

            switch ($type) {
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
                    
                case 'template':
                    if (Mage::registry('website')->getIsAdmin()) {
                        $dir .= DS.'view'.DS.'admin';
                    } else {
                        $dir .= DS.'view'.DS.'front';
                    }
                    $dir .= DS.'template';
                    break;
                    
                case 'layout':
                    if (Mage::registry('website')->getIsAdmin()) {
                        $dir .= DS.'view'.DS.'admin';
                    } else {
                        $dir .= DS.'view'.DS.'front';
                    }
                    $dir .= DS.'layout';
                    break;
                    
                case 'translate':
                    if (Mage::registry('website')->getIsAdmin()) {
                        $dir .= DS.'view'.DS.'admin';
                    } else {
                        $dir .= DS.'view'.DS.'front';
                    }
                    $dir .= DS.'translate';
                    break;

                case 'var':
                    $dir = dirname($dir).DS.'var';
                    break;
                    
                case 'media':
                    $dir = dirname($dir).DS.'www'.DS.'media';
                    break;
            }
        }

        return $dir;
    }

    public function getBaseUrl($params=array())
    {
        if (isset($params['_admin'])) {
            $isAdmin = $params['_admin'];
        } else {
            $isAdmin = Mage::registry('website')->getIsAdmin();
        }
        if (!$isAdmin) {
            if (empty($params['_website'])) {
                $params['_website'] = Mage::registry('website')->getCode();
            }
            if (!empty($_SERVER['HTTPS'])) {
                if (!empty($params['_type']) && ('skin'===$params['_type'] || 'js'===$params['_type'])) {
                    $params['_secure'] = true;
                }
            }
            $websiteConfig = Mage::getConfig()->getWebsiteConfig($params['_website']);
            $urlConfig = empty($params['_secure']) ? $websiteConfig->unsecure : $websiteConfig->secure;
    
            $protocol = (string)$urlConfig->protocol;
            $host = (string)$urlConfig->host;
            $port = (int)$urlConfig->port;
            $basePath = (string)$urlConfig->basePath;

            $url = $protocol.'://'.$host;
            $url .= ('http'===$protocol && 80===$port || 'https'===$protocol && 443===$port) ? '' : ':'.$port;
            $url .= empty($basePath) ? '/' : $basePath;
        } else {
            $url = dirname($_SERVER['SCRIPT_NAME']).'/';
        }

        if (isset($params['_type'])) {
            switch ($params['_type']) {
                case 'skin':
                    $url .= 'skins/default/';
                    break;

                case 'js':
                    $url .= 'js/';
                    break;
                    
                case 'media':
                    $url .= 'media/';
                    break;
            }
        }

        return $url;
    }


    public function getRouterInstance($routerName='', $singleton=true)
    {
        $routers = $this->getNode('front/routers');
        if (!empty($routerName)) {
            $routerConfig = $routers->$routerName;
        } else {
            foreach ($routers as $routerConfig) {
                if ($routerConfig->is('default')) {
                    break;
                }
            }
        }
        $className = $routerConfig->getClassName();
        $constructArgs = $routerConfig->args;
        if (!$className) {
            $className = 'Mage_Core_Controller_Front_Router';
        }
        if ($singleton) {
            $regKey = '_singleton_router_'.$routerName;
            if (!Mage::registry($regKey)) {
                Mage::register($regKey, new $className($constructArgs));
            }
            return Mage::registry($regKey);
        } else {
            return new $className($constructArgs);
        }
    }

    /**
     * Load event observers for an area (front, admin)
     *
     * @param string $area
     * @return boolean
     */
    public function loadEventObservers($area)
    {
        if ($events = $this->getNode("$area/events")) {
            $events = $events->children();
        }
        else {
            return false;
        }
        
        foreach ($events as $event) {
            $eventName = $event->getName();
            $observers = $event->observers->children();
            foreach ($observers as $observer) {
                $callback = array($observer->getClassName(), (string)$observer->method);
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
    public function getPathVars($args=null)
    {
        $path = array();

        $path['baseUrl'] = Mage::getBaseUrl();
        $path['baseSkinUrl'] = Mage::getBaseUrl(array('_type'=>'skin'));
        $path['baseJsUrl'] = Mage::getBaseUrl(array('_type'=>'js'));
        $path['baseSecureUrl'] = Mage::getBaseUrl(array('_secure'=>true));

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

        $modules = $this->getNode('modules');
        foreach ($modules as $module) {
            $area = $module->$areaName;
            if (empty($area)) {
                continue;
            }
            $load = $area->useModuleSteps;
            if (!$area->is('useModuleSteps')) {
                continue;
            }
            $callback = array($moduleName.'_Module_'.uc_words($areaName), $methodName);
            if (is_callable($callback)) {
                call_user_func($callback);
            }
        }
    }

    public function getModelClassName($model, $class)
    {
        $config = $this->getNode('global/models/'.$model);

        if (isset($config->subs->$class)) {
            $className = (string)$config->subs->$class;
        } else {
            $className = $config->getClassName();

            if (''!==$class) {
                $className .= '_'.uc_words($class);
            }
        }
        return $className;
    }

    /**
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog', 'product')
     *
     * Will instantiate Mage_Catalog_Model_Mysql4_Product
     *
     * @param string $model
     * @param string $class
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract
     */
    public function getModelInstance($model, $class='', $constructArguments=array())
    {
        $className = $this->getModelClassName($model, $class);
        
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
        return $this->getNode("global/resources/$name");
    }

    public function getResourceConnectionConfig($name)
    {
        $config = $this->getResourceConfig($name);
        $conn = $config->connection;
        if (!empty($conn->use)) {
            return $this->getResourceConnectionConfig((string)$conn->use);
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
    public function getResourceTypeConfig($type)
    {
        return $this->getNode("global/resource/connection/types/$type");
    }

     /**
     * Get block type(s) from config
     *
     * @param string $type
     * @return Varien_Simplexml_Object
     */
    public function getBlockTypeConfig($type='')
    {
        $types = $this->getNode("global/block/types");
        if (''===$type) {
            return $types;
        } else {
            return $types->$type;
        }
    }

    public function getWebsiteConfig($website='default')
    {
        return $this->getNode("global/websites/$website");
    }

    /**
     * Get domain configuration
     *
     * @param   stting $name
     * @return  Varien_Simplexml_Object
     */
    public function getDomain($name)
    {
        return $this->getNode("domains/$name");
    }

    /**
     * Get current domain configuration
     *
     * @return  Varien_Simplexml_Object
     */
    public function getCurrentDomain()
    {
        return $this->getDomain('base');#::registry('website')->getDomain());
    }

}