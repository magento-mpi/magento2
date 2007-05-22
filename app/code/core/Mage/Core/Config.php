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
        $this->getCache()->setDir($this->getBaseDir('cache_config'))->setKey('globalConfig');
        
        $this->loadGlobal();
    }

    /**
     * Config load sequence. Executed only in case of missing cache
     *
     * @return boolean
     */
    function loadGlobal()
    {
        if ($this->getCache()->load()) {
            return true;
        }
        
        $this->loadCore();
        $this->loadModules();
        $this->loadLocal();
        $this->applyExtends();
        
        $this->getCache()->save();

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
        $this->getCache()->addComponent($configFile);
        $this->setXml($configFile, 'file');

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
            $this->getCache()->addComponent($configFile);
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
    public function loadLocal()
    {
        $configFile = Mage::getBaseDir('etc').DS.'local.xml';
        
        if (is_file($configFile)) {
            //die('File ' . $configFile . ' not found. Copy it from ' . $configFile . '.dev');
            $this->getCache()->addComponent($configFile);
            $localConfig = $this->loadFile($configFile);
            $this->_xml->extend($localConfig, true);
        } else {
            $string = $this->getLocalDist();
            $localConfig = $this->loadString($string);
            $this->_xml->extend($localConfig, true);
            $this->getCache()->setIsAllowedToSave(false);
        }
        return $this;
    }
    
    public function getTempVarDir()
    {
        return (!empty($_ENV['TMP']) ? empty($_ENV['TMP']) : '/tmp/magento').'/var';
    }
        
    public function getLocalDist()
    {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $subst = array(
            '{root_dir}'=>dirname(Mage::getRoot()),
            '{var_dir}'=>$this->getTempVarDir(),
            '{protocol}'=>'http',
            '{host}'=>$_SERVER['SERVER_NAME'],
            '{port}'=>$_SERVER['SERVER_PORT'],
            '{base_path}'=>$basePath==='/' ? '/' : $basePath.'/',
        );
        $template = file_get_contents($this->getBaseDir('etc').DS.'local.xml.template');
        $template = str_replace(array_keys($subst), array_values($subst), $template);

        return $template;
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
    function getModuleConfig($moduleName='')
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
                $module = $this->getModuleConfig($module);
            }
            if (isset($module->setup)) {
                $moduleClassName = $module->setup->getClassName();
                if (!empty($moduleClassName)) {
                    $className = $moduleClassName;
                }
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
     * @return string
     */
    public function getBaseDir($type)
    {
        $dir = (string)$this->getNode('global/default/filesystem/'.$type);
        if (!$dir) {
            $dir = Mage::getRoot();
            switch ($type) {
                case 'etc':
                    $dir = Mage::getRoot().DS.'etc';
                    break;
                    
                case 'code':
                    $dir = Mage::getRoot().DS.'code';
                    break;
                    
                case 'var':
                    $dir = $this->getTempVarDir();
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    break;
                    
                case 'session':
                    $dir = $this->getBaseDir('var').DS.'session';
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    break;
                    
                case 'cache_config':
                    $dir = $this->getBaseDir('var').DS.'cache'.DS.'config';
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    break;
                                        
                case 'cache_layout':
                    $dir = $this->getBaseDir('var').DS.'cache'.DS.'layout';
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    break;
                    
            }
        }
        if (!$dir) {
            throw Mage::exception('Mage_Core', 'Invalid base dir type specified: '.$type);
        }

        return $dir;
    }
    
    public function getModuleDir($type, $moduleName)
    {
        $codePool = (string)$this->getModuleConfig($moduleName)->codePool;
        $dir = $this->getBaseDir('code').DS.$codePool.DS.uc_words($moduleName, DS);

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
            if ($websiteConfig) {
                $urlConfig = empty($params['_secure']) ? $websiteConfig->unsecure : $websiteConfig->secure;
        
                $protocol = (string)$urlConfig->protocol;
                $host = (string)$urlConfig->host;
                $port = (int)$urlConfig->port;
                $basePath = (string)$urlConfig->basePath;
    
                $url = $protocol.'://'.$host;
                $url .= ('http'===$protocol && 80===$port || 'https'===$protocol && 443===$port) ? '' : ':'.$port;
                $url .= empty($basePath) ? '/' : $basePath;
            }
            else {
                $url = dirname($_SERVER['SCRIPT_NAME']);
                $url = strlen($url)>1 ? $url.'/' : '/';
            }
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
        if ($config) {
            $conn = $config->connection;
            if (!empty($conn->use)) {
                return $this->getResourceConnectionConfig((string)$conn->use);
            } else {
                return $conn;
            }
        }
        return false;
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

    public function getWebsiteConfig($website='base')
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