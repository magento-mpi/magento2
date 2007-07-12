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

class Mage_Core_Model_Config extends Varien_Simplexml_Config
{
    protected $_classNameCache = array();

    protected $_blockClassNameCache = array();
    /**
     * Constructor
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->_elementClass = 'Mage_Core_Model_Config_Element';
    }

    /**
     * Initialization of core config
     *
     */
    function init()
    {
        $this->getCache()->setDir($this->getBaseDir('etc'))->setKey('config-compiled');
        
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
        $this->setXml($this->loadFile($configFile));

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
            $configFile = $this->getModuleDir('etc', $module->getName()).DS.'config.xml';
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
        
        try {
            $this->getCache()->addComponent($configFile);
            $localConfig = $this->loadFile($configFile);
            $this->_xml->extend($localConfig, true);
        }
        catch (Exception $e){
            $string = $this->getLocalDist($this->getLocalServerVars());
            $localConfig = $this->loadString($string);
            $this->_xml->extend($localConfig, true);
            $this->getCache()->setIsAllowedToSave(false);
        }
        if (is_file($configFile)) {
            //die('File ' . $configFile . ' not found. Copy it from ' . $configFile . '.dev');
        } else {
        }
        return $this;
    }
    
    public function getTempVarDir()
    {
        return (!empty($_ENV['TMP']) ? $_ENV['TMP'] : '/tmp').'/magento/var';
    }
        
    public function getLocalDist($data)
    {
        $template = file_get_contents($this->getBaseDir('etc').DS.'local.xml.template');
        foreach ($data as $index=>$value) {
            $template = str_replace('{{'.$index.'}}', '<![CDATA['.$value.']]>', $template);
        }
        return $template;
    }
    
    public function getLocalServerVars()
    {
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ("\\"==$basePath || "/"==$basePath) {
            $basePath = '/';
        } else {
            $basePath .= '/';
        }
        $host = explode(':', $_SERVER['HTTP_HOST']);
        $serverName = $host[0];
        $serverPort = isset($host[1]) ? $host[1] : (isset($_SERVER['HTTPS']) ? '443' : '80');
        
        $arr = array(
            'root_dir'  => dirname(Mage::getRoot()),
            'var_dir'   => $this->getTempVarDir(),
            'protocol'  => isset($_SERVER['HTTPS']) ? 'https' : 'http',
            'host'      => $serverName,
            'port'      => $serverPort,
            'base_path' => $basePath,
        );
        return $arr;
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
            Mage::getResourceModel('core/config')->updateXmlFromDb($this->_xml);
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
     * @todo get global dir config
     * @param string $type
     * @return string
     */
    public function getBaseDir($type)
    {
        $dir = (string)$this->getNode('stores/base/system/filesystem/'.$type);

        if (!$dir) {
            $dir = $this->getDefaultBaseDir($type);
        }
        if (!$dir) {
            throw Mage::exception('Mage_Core', 'Invalid base dir type specified: '.$type);
        }
        switch ($type) {
            case 'var': case 'session': case 'cache_config': case 'cache_layout': case 'cache_block':
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                break;
        }
        
        $dir = str_replace('/', DS, $dir);

        return $dir;
    }
    
    public function getDefaultBaseDir($type)
    {
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
                break;
                
            case 'session':
                $dir = $this->getBaseDir('var').DS.'session';
                break;
                
            case 'cache_config':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'config';
                break;
                                    
            case 'cache_layout':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'layout';
                break;
                
            case 'cache_block':
                $dir = $this->getBaseDir('var').DS.'cache'.DS.'block';
                break;
                
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
        
        $dir = str_replace('/', DS, $dir);
        
        return $dir;
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
            $regKey = '_singleton_router/'.$routerName;
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
                switch ((string)$observer->type) {
                    case 'singleton':
                        $callback = array(
                            Mage::getSingleton((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    case 'object':
                    case 'model':
                        $callback = array(
                            Mage::getModel((string)$observer->class),
                            (string)$observer->method
                        );
                        break;
                    default:
                        $callback = array($observer->getClassName(), (string)$observer->method);
                        break;
                }
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

    public function getGrouppedClassName($groupRootNode, $class)
    {
        if (isset($this->_classNameCache[$groupRootNode][$class])) {
            return $this->_classNameCache[$groupRootNode][$class];
        }
        
        $config = $this->getNode($groupRootNode);
        if (empty($config)) {
            return false;
        }

        if (isset($config->classes->$class)) {
            $className = (string)$config->classes->$class;
        } else {
#echo $groupRootNode.', '.$class.'<hr>';
            $className = $config->getClassName();
#if (stripos($class, 'manufacturer')!==false) echo "TEST:".$className;

            if (''!==$class) {
                $className .= '_'.uc_words($class);
            }
        }
        
        $this->_classNameCache[$groupRootNode][$class] = $className;
        
        return $className;
    }
    
    public function getBlockClassName($blockType)
    {
        $typeArr = explode('/', $blockType);
        if (!empty($typeArr[1])) {
            return $this->getGrouppedClassName('global/block/groups/'.$typeArr[0], $typeArr[1]);
        } else {
            if (isset($this->_blockClassNameCache[$blockType])) {
                return $this->_blockClassNameCache[$blockType];
            }
            $className = $this->getNode('global/block/types/'.$typeArr[0])->getClassName();
            $this->_blockClassNameCache[$blockType] = $className;
            return $className;
        }
    }
    
    public function getModelClassName($modelClass)
    {
        $classArr = explode('/', $modelClass);
        if (!isset($classArr[1])) {
            return $modelClass;
        }
        return $this->getGrouppedClassName('global/models/'.$classArr[0], $classArr[1]);
    }

    /**
     * Get model class instance.
     *
     * Example:
     * $config->getModelInstance('catalog/product')
     *
     * Will instantiate Mage_Catalog_Model_Mysql4_Product
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return Mage_Core_Model_Abstract
     */
    public function getModelInstance($modelClass='', $constructArguments=array())
    {
        $className = $this->getModelClassName($modelClass);
        return new $className($constructArguments);
    }
    
    public function getNodeClassInstance($path)
    {
        $config = Mage::getConfig()->getNode($path);
        if (!$config) {
            return false;
        } else {
            $className = $config->getClassName();
            return new $className();
        }
    }
    
    public function getResourceModelInstance($modelClass='', $constructArguments=array())
    {

        $classArr = explode('/', $modelClass);
        $resourceModel = (string)$this->getNode('global/models/'.$classArr[0].'/resourceModel');
        if (!$resourceModel) {
            return false;
        }
        return $this->getModelInstance($resourceModel.'/'.$classArr[1], $constructArguments);
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
}