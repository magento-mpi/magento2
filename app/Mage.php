<?php

/**
 * Just a shortcut for popular constant :)
 */
define ('DS', DIRECTORY_SEPARATOR);

include "code/core/Mage/Core/Profiler.php";

function __autoload($class)
{
    #echo $class."<hr>";
    #Mage_Core_Profiler::setTimer('autoload');
    $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class))).'.php';
    include $classFile;
    #Mage_Core_Profiler::setTimer('autoload', true);
}

/**
 * Main Mage hub class
 *
 * @author Moshe Gurvich <moshe@varien.com>
 * @author Andrey Korolyov <andrey@varien.com>
 */
final class Mage {

    static private $_configSections = array();

    /**
     * Application root absolute path
     *
     * @var string
     */
    static private $_appRoot = null;

    /**
     * Code pools
     *
     * include_path will be built with the order these are specified
     *
     * defined in core.ini [codepools]
     * default: local, community, core
     *
     * @var array
     */
    static private $_codePools = array();

    /**
     * Collection of objects with information about modules installed
     *
     * @var array
     */
    static private $_moduleInfo = array();

    /**
     * Set application root absolute path
     *
     * @param string $appRoot
     */
    public static function setRoot($appRoot='')
    {
    	if (''===$appRoot) {
    	    // automagically find application root by dirname of Mage.php
    		$appRoot = dirname(__FILE__);
    	}

    	$appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            self::$_appRoot= $appRoot;
        } else {
            Mage::exception($appRoot.' is not a directory or not readable by this user');
        }
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     */
    public static function getRoot($type='')
    {
        $dir = self::$_appRoot;
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
        }
        return $dir;
    }

    /**
     * Add a directory to code pool
     *
     * @param string $name code pool name
     * @param string $dir code pool directory
     */
    public static function addCodePool($name, $dir)
    {
        self::$_codePools[$name] = $dir;
    }

    /**
     * Retrieve code pools
     *
     * @return array
     */
    public static function getCodePools()
    {
        return self::$_codePools;
    }

    /**
     * Add Module Information
     *
     * @param Zend_Config $data
     * @param string $moduleInfoClass
     */
    public static function addModule($data, $moduleInfoClass='Mage_Core_Module_Info')
    {
        if (empty($data) || !is_object($data)) {
            Mage::exception('Invalid data');
        }

        if ($moduleInfoClass!='Mage_Core_Module_Info'
            && is_subclass_of($moduleInfoClass, 'Mage_Core_Module_Info')) {
            Mage::exception('Invalid module info class name');
        }

        self::$_moduleInfo[strtolower($data->name)] = new $moduleInfoClass($data);
    }

    /**
     * Retrieve module information object
     *
     * @param string $module
     * @return Mage_Core_Module_Info
     */
    public static function getModuleInfo($module='')
    {
        $module = strtolower($module);
        if (''===$module) {
            return self::$_moduleInfo;
        } else {
            if (isset(self::$_moduleInfo[$module])) {
                return self::$_moduleInfo[$module];
            } else {
                return false;
            }
        }
    }

    /**
     * Get module configuration, loaded from config files
     *
     * @param string $module
     * @param string $key
     * @return Zend_Config_Ini
     */
    public static function getModuleConfig($module, $key='')
    {
        return self::getModuleInfo($module)->getConfig($key);
    }

    /**
     * Add handler for parsing a config file section
     *
     * @param string $name
     * @param callback $callback
     */
    static function addConfigSection($name, $callback)
    {
        self::$_configSections[$name] = $callback;
    }

    /**
     * Retrieve config section handler class
     *
     * @param string $name
     * @return string
     */
    static function getConfigSection($name='')
    {
        if (''===$name) {
            return self::$_configSections;
        } else {
            if (isset(self::$_configSections[$name])) {
                return self::$_configSections[$name];
            }
        }
        return false;
    }

    /**
     * Retrieve event object
     *
     * @param string $name
     * @return Mage_Core_Event
     */
    public static function getEvent($name)
    {
        return Mage_Core_Event::getEvent($name);
    }

    /**
     * Add event object
     *
     * @param unknown_type $name
     */
    public static function addEvent($name)
    {
        return Mage_Core_Event::addEvent($name);
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, array $arguments=array(), $observerName='')
    {
        return Mage_Core_Event::addObserver($eventName, $callback, $arguments, $observerName);
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public static function addMultiObserver($eventRegex, $callback, $observerName='')
    {
        return Mage_Core_Event::addMultiObserver($eventRegex, $callback, $observerName);
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiobservers matching event name pattern
     *
     * @param string $name
     * @param array $args
     */
    public static function dispatchEvent($name, array $args=array())
    {
        return Mage_Core_Event::dispatchEvent($name, $args);
    }
    
    public static function getController()
    {
        return Mage_Core_Controller::getController();
    }


    /**
     * Retrieve active modules
     *
     * @return array
     */
    public static function getActiveModules()
    {
        return self::getModuleInfo();
    }

    /**
     * Load active modules
     * 
     * @param   mixed $configs
     */
    public static function loadActiveModules($configs = '')
    {
        $modules = self::getActiveModules();
        foreach ($modules as $modName=>$modInfo) {
            $modInfo->loadConfig('load');
            $modInfo->loadConfig('*user*');
            if (!empty($configs)) {
                if (is_array($configs)) {
                    foreach ($configs as $config) {
                        $modInfo->loadConfig($config);
                    }
                }
                else {
                    $modInfo->loadConfig($configs);
                }
            }
            $modInfo->processConfig();
        }
    }

    /**
     * Create page block
     *
     * See {@link Mage_Core_Block::createBlock}
     *
     * @param string $type
     * @param string $name
     * @param array $attributes
     * @return Mage_Core_Block_Abstract
     */
    public static function createBlock($type, $name='', array $attributes=array())
    {
        return Mage_Core_Block::createBlock($type, $name, $attributes);
    }

    /**
     * Create block from template
     *
     * @link Mage_Core_Block::createBlockLike
     * @param string $template
     * @param string $name
     * @param array $attributes
     * @return Mage_Core_Block_Abstract
     */
    public static function createBlockLike($template, $name='', array $attributes=array())
    {
        return Mage_Core_Block::createBlockLike($template, $name, $attributes);
    }

    /**
     * Return Block object for block id
     *
     * @link    Mage_Core_Block::getBlockByName
     * @param   string $id
     * @return  Mage_Core_Block_Abstract
     */
    public static function getBlock($name)
    {
        return Mage_Core_Block::getBlockByName($name);
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($type='')
    {
        return Mage_Core_Controller::getBaseUrl($type);
    }
    
	/**
	 * Get model class
	 *
	 * @link Mage_Core_Model::getModelClass
	 * @param string $model
	 * @param string $class
	 * @param array $arguments
	 * @return Mage_Core_Model_Abstract
	 */
	public static function getModel($model, $class='', array $arguments=array())
	{
	    return Mage_Core_Model::getModelClass($model, $class, $arguments);
	}

	public static function getCurentWebsite()
	{
		return Mage_Core_Website::getWebsiteId();
	}

	/**
	 * Throw new exception by module
	 *
	 * @param string $message
	 * @param integer $code
	 * @param string $module
	 */
	public static function exception($message, $code=0, $module='Mage_Core')
	{
	    $className = $module.'_Exception';
	    throw new $className($message, $code);
	}
	
    /**
     * Initialize Mage
     *
     * @param string $appRoot
     */
    private static function init($appRoot='')
    {
        // set application root path
        self::setRoot($appRoot);

        // load core config file
        $coreConfig = new Zend_Config_Ini(self::getRoot('etc')
            .DS.'core.ini', null, true);

        // load codepools
        foreach ($coreConfig->codepools as $pool=>$active) {
            if ($active) {
                self::addCodePool($pool, self::getRoot('code').DS.$pool);
            }
        }

        // set include path constructed from codepools and previous include path
        $include_path = get_include_path();
        $include_path = str_replace('.'.PATH_SEPARATOR, '', $include_path);
        $include_path = '.'.PATH_SEPARATOR
            .join(PATH_SEPARATOR, self::getCodePools())
            .PATH_SEPARATOR.$include_path;
        set_include_path($include_path);

        Mage_Core_Profiler::setTimer('app');

        // load modules list
        $modulesConfig = new Zend_Config_Ini(self::getRoot('etc')
            .DS.'modules.ini', null, true);

        // add module information classes for each module from the list
        foreach($modulesConfig as $ns=>$modules) {
            foreach($modules as $name=>$data) {
                if ($data->active) {
                    $data->name = $ns.'_'.$name;
                    self::addModule($data);
                }
            }
        }
    }

    /**
     * Mage main entry point
     *
     * @param string $appRoot
     */
    public static function run($appRoot='')
    {
        try {
            self::init($appRoot);
            #Mage_Core_Profiler::setTimer('zend_controller');
            Mage_Core_Controller::setController(new Mage_Core_Controller_Zend());
            #Mage_Core_Profiler::setTimer('zend_controller', true);
            self::loadActiveModules('front_load');
            Mage_Core_Controller::getController()->run();
            
            Mage_Core_Profiler::getTimer('app', true);
            Mage_Core_Profiler::getSqlProfiler();
        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }
    
    public static function runAdmin($appRoot='')
    {
        // temp (for test)
        session_start();
        try {            
            self::init($appRoot);
            Mage_Core_Controller::setController(new Mage_Core_Controller_Zend_Admin());
            self::loadActiveModules('admin_load');
            Mage_Core_Controller::getController()->run();
            
            //Mage_Core_Profiler::getTimer('app', true);
          //  Mage_Core_Profiler::getSqlProfiler();
        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }
}