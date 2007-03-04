<?php

/**
 * Just a shortcut for popular constant :)
 */
define ('DS', DIRECTORY_SEPARATOR);

function __autoload($class)
{
    #echo $class."<hr>";
    #Ecom::setTimer('autoload');
    $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class))).'.php';
    include_once $classFile;
    #Ecom::setTimer('autoload', true);
}

/**
 * Our main library is Zend
 */
#require_once 'Zend.php';

/**
 * Our configuration format of choice is .ini file
 * for performance and flexibility
 */
#require_once 'Zend/Config/Ini.php';

/**
 * Main Ecom hub class
 *
 * @author Moshe Gurvich <moshe@varien.com>
 * @author Andrey Korolyov <andrey@varien.com>
 */
final class Ecom {

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
     * Collection of events objects
     *
     * @var array
     */
    static private $_events = array();

    /**
     * Collection of observers to watch for multiple events by regex
     *
     * @var unknown_type
     */
    static private $_multiObservers = array();

    /**
     * Controller
     *
     * @var    Ecom_Core_Controller_Varien
     */
    static private $_controller;

    /**
     * Timers for code profiling
     *
     * @var array
     */
    static private $_timers;
    
    /**
     * Cumulative timers
     * 
     * @var array
     */
    static private $_cumulativeTimers;

    /**
     * Layout name
     *
     * @var string
     */
    static private $_layout;

    /**
     * Set application root absolute path
     *
     * @param string $appRoot
     */
    public static function setAppRoot($appRoot='')
    {
    	if (''===$appRoot) {
    	    // automagically find application root by dirname of Ecom.php
    		$appRoot = dirname(__FILE__);
    	}

    	$appRoot = realpath($appRoot);

        if (is_dir($appRoot) and is_readable($appRoot)) {
            self::$_appRoot= $appRoot;
        } else {
            Ecom::exception($appRoot.' is not a directory or not readable by this user');
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
    public static function addModule($data, $moduleInfoClass='Ecom_Core_Module_Info')
    {
        if (empty($data) || !is_object($data)) {
            Ecom::exception('Invalid data');
        }

        if ($moduleInfoClass!='Ecom_Core_Module_Info'
            && is_subclass_of($moduleInfoClass, 'Ecom_Core_Module_Info')) {
            Ecom::exception('Invalid module info class name');
        }

        self::$_moduleInfo[strtolower($data->name)] = new $moduleInfoClass($data);
    }

    /**
     * Retrieve module information object
     *
     * @param string $module
     * @return Ecom_Core_Module_Info
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
     * Retrieve module main class object
     *
     * @param string $module
     * @return Ecom_Core_Module_Abstract
     */
    public static function getModuleClass($module)
    {
        return self::getModuleInfo($module)->getClass();
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
     * @return Ecom_Core_Event
     */
    public static function getEvent($name)
    {
        $name = strtolower($name);

        if (isset(self::$_events[$name])) {
            return self::$_events[$name];
        }
        return false;
    }

    /**
     * Add event object
     *
     * @param unknown_type $name
     */
    public static function addEvent($name)
    {
        $name = strtolower($name);

        if (!self::getEvent($name)) {
            self::$_events[$name] = new Ecom_Core_Event(array('name'=>$name));
        }
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
        if (!self::getEvent($eventName)) {
            self::addEvent($eventName);
        }
        $observer = new Ecom_Core_Event_Observer($callback, $arguments);
        self::getEvent($eventName)->addObserver($observer, $observerName);
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public static function addMultiObserver($eventRegex, $callback, $observerName='')
    {
        $this->_multiObservers[$eventRegex] = $callback;
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
        $event = self::getEvent($name);
        if ($event && $event->getObservers()) {
            $event->dispatch($args);
        }

        $args['_eventName'] = $name;
        foreach (self::$_multiObservers as $regex=>$callback) {
            if (preg_match('#'.$regex.'#i', $name)) {
                call_user_func_array($callback, $args);
            }
        }
    }

    /**
     * Load required classes, run right after set_include_path
     *
     */
    private static function loadRequiredClasses()
    {
        #include_once 'Ecom/Core/Exception.php';
        #include_once 'Ecom/Core/Module/Info.php';
        #include_once 'Ecom/Core/Event.php';
        #include_once 'Ecom/Core/Event/Observer.php';
        #include_once 'Ecom/Core/Resource.php';
        #include_once 'Ecom/Core/Model.php';
        #include_once 'Ecom/Core/Block.php';
        #include_once 'Ecom/Core/Website.php';
    }

    /**
     * Initialize Ecom
     *
     * @param string $appRoot
     */
    private static function init($appRoot='')
    {
        // set application root path
        self::setAppRoot($appRoot);

        // load core config file
        $coreConfig = new Zend_Config_Ini(self::getRoot('etc')
            .DS.'core.ini', null, true);

        // set default layout name
        self::setLayout($coreConfig->layout->name);

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

        // load classes that have to be loaded from start
        self::loadRequiredClasses();

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

        // load default controller
        #include_once 'Ecom/Core/Controller/Zend.php';
        self::setController(new Ecom_Core_Controller_Zend());
    }

    /**
     * Load class
     *
     * @param string $class
     * @param array $dirs
     */
    public static function loadClass($class, $dirs = null)
    {
        static $_classes = array();
        return false;
/*
        if (is_null($dirs)) {
            $dirs = self::$_classDirs;
        }
*/
        #Ecom::setTimer(__METHOD__);
        
       
        if (!isset($_classes[$class])) {
            $classFile = str_replace('_', DS, $class).'.php';
            try {
                #include_once $classFile;
            } catch (Exception $e) {
                throw Ecom::exception('Failed to load class '.$class);
            }
            $_classes[$class] = $classFile;
        }
        
        #Ecom::setTimer(__METHOD__, true);
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
     */
    public static function loadActiveModules()
    {
        foreach (self::getActiveModules() as $moduleName=>$moduleInfo) {
            self::getModuleClass($moduleName);
        }
    }

    /**
     * Apply db updates to all active modules
     *
     */
	public static function applyDbUpdates()
	{
        #include_once 'Ecom/Core/Module/Setup.php';

	    $modules = self::getActiveModules();
	    foreach ($modules as $name=>$info) {
            $setup = new Ecom_Core_Module_Setup(self::getModuleClass($name));
            $setup->applyDbUpdates();
	    }
	}

	/**
     * Ecom main entry point
     *
     * @param string $appRoot
     */
    public static function run($appRoot='')
    {
        try {

            Ecom::setTimer('app');

            self::init($appRoot);

            self::loadActiveModules();

            self::applyDbUpdates();

            self::getModuleClass('Ecom_Core')->run();

            Ecom::getTimer('app', true);

            Ecom::getSqlProfiler();

        } catch (Zend_Exception $e) {

            echo $e->getMessage()."<pre>".$e->getTraceAsString();

        } catch (PDOException $e) {

            echo $e->getMessage()."<pre>".$e->getTraceAsString();

        }
    }

    private static function initAdmin($appRoot='')
    {
        // set application root path
        self::setAppRoot($appRoot);

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

        // load classes that have to be loaded from start
        self::loadRequiredClasses();

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

        //load default controller
        #include_once 'Ecom/Core/Controller/Zend/Admin.php';
        self::setController(new Ecom_Core_Controller_Zend_Admin());
    }


    public static function runAdmin($appRoot='')
    {
        // temp (for test)
        session_start();
        try {
            //Ecom::setTimer('app');
            self::initAdmin($appRoot);
            self::loadActiveModules();
            self::getModuleClass('Ecom_Core')->run();
            //Ecom::getTimer('app', true);
          //  Ecom::getSqlProfiler();
        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }

    /**
     * Set default controller
     *
     * @param Ecom_Core_Controller_Zend $controller
     */
    public static function setController($controller)
    {
        self::$_controller  = $controller;
    }

    /**
     * Get Controller
     *
     * @param     none
     * @return	  Ecom_Core_Controller_Zend
     * @author	  Soroka Dmitriy <dmitriy@varien.com>
     */
    public static function getController()
    {
    	return self::$_controller;
    }

    /**
     * Create page block
     *
     * See {@link Ecom_Core_Block::createBlock}
     *
     * @param string $type
     * @param string $name
     * @param array $attributes
     * @return Ecom_Core_Block_Abstract
     */
    public static function createBlock($type, $name='', array $attributes=array())
    {
        return Ecom_Core_Block::createBlock($type, $name, $attributes);
    }

    /**
     * Create block from template
     *
     * @link Ecom_Core_Block::createBlockLike
     * @param string $template
     * @param string $name
     * @param array $attributes
     * @return Ecom_Core_Block_Abstract
     */
    public static function createBlockLike($template, $name='', array $attributes=array())
    {
        return Ecom_Core_Block::createBlockLike($template, $name, $attributes);
    }

    /**
     * Return Block object for block id
     *
     * @link    Ecom_Core_Block::getBlockByName
     * @param   string $id
     * @return  Ecom_Core_Block_Abstract
     */
    public static function getBlock($name)
    {
        return Ecom_Core_Block::getBlockByName($name);
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($type='')
    {
        $url = self::getController()->getRequest()->getBaseUrl();

        switch ($type) {
            case 'skin':
                $url .= '/skins/default';
                break;

            case 'js':
                $url .= '/js';
                break;
        }

        return $url;
    }

	/**
	 * Set timer to current microtime and return delta from previous timer value
	 *
	 * @param string $timerName
	 * @return float
	 */
	public static function setTimer($timerName, $cumulative=false)
	{
	    if (!$cumulative) {
    		$oldTimer = isset(self::$_timers[$timerName]) ? self::$_timers[$timerName] : false;
    		self::$_timers[$timerName] = microtime(true);
    		return self::$_timers[$timerName]-$oldTimer;
	    } else {
	        if (!isset(self::$_cumulativeTimers[$timerName])) {
	           self::$_cumulativeTimers[$timerName] = array(0, 0);
	       }
	       self::$_cumulativeTimers[$timerName][0] += self::getTimer($timerName);
	       self::$_cumulativeTimers[$timerName][1] ++;
	    }
	}

	/**
	 * Get delta from previous timer value and print if requested
	 *
	 * @param string $timerName
	 * @param boolean $print
	 * @return float
	 */
	public static function getTimer($timerName, $print=false)
	{
		if (!isset(self::$_timers[$timerName])) {
			return false;
		}
		$delta = microtime(true)-self::$_timers[$timerName];
		if ($print) {
    		echo "<hr>$timerName execution time: $delta<hr>";
		}
		return $delta;
	}
	
	public static function getCumulativeTimer($timerName='')
	{
	    if (''===$timerName) {
	        return self::$_cumulativeTimers;
	    }
	    
		if (!isset(self::$_cumulativeTimers[$timerName])) {
			return false;
		}
		return self::$_cumulativeTimers[$timerName];
	}

	/**
	 * Get model class
	 *
	 * @link Ecom_Core_Model::getModelClass
	 * @param string $model
	 * @param string $class
	 * @param array $arguments
	 * @return Ecom_Core_Model_Abstract
	 */
	public static function getModel($model, $class='', array $arguments=array())
	{
	    return Ecom_Core_Model::getModelClass($model, $class, $arguments);
	}

	/**
	 * Get default module name for default page
	 *
	 * @return string
	 */
	public static function getDefaultModule() {
        return $this->_defaultModuleName;
	}

	/**
	 * Throw new exception by module
	 *
	 * @param string $message
	 * @param integer $code
	 * @param string $module
	 */
	public static function exception($message, $code=0, $module='Ecom_Core')
	{
	    $className = $module.'_Exception';
	    #self::loadClass($className);
	    throw new $className($message, $code);
	}

	/**
	 * Set default layout
	 *
	 * @param string $layout
	 */
	public static function setLayout($layout)
	{
		self::$_layout = $layout;
	}

	/**
	 * Get default layout
	 *
	 * @return string
	 */
	public static function getLayout()
	{
		return self::$_layout;
	}

	/**
	 * Output SQl Zend_Db_Profiler
	 *
	 */
	public static  function getSqlProfiler() {
	    $res = Ecom_Core_Resource::getResource('dev_write')->getConnection();
	    $profiler = $res->getProfiler();
        $totalTime    = $profiler->getTotalElapsedSecs();
        $queryCount   = $profiler->getTotalNumQueries();
        $longestTime  = 0;
        $longestQuery = null;

        foreach ($profiler->getQueryProfiles() as $query) {
            if ($query->getElapsedSecs() > $longestTime) {
                $longestTime  = $query->getElapsedSecs();
                $longestQuery = $query->getQuery();
            }
        }

        echo 'Executed ' . $queryCount . ' queries in ' . $totalTime . ' seconds' . "<br>";
        echo 'Average query length: ' . $totalTime / $queryCount . ' seconds' . "<br>";
        echo 'Queries per second: ' . $queryCount / $totalTime . "<br>";
        echo 'Longest query length: ' . $longestTime . "<br>";
        echo 'Longest query: <br>' . $longestQuery . "<hr>";
        
        echo '<pre>cumulative: '.print_r(Ecom::getCumulativeTimer(),1).'</pre>';
	}

	public static function getCurentWebsite()
	{
		return Ecom_Core_Website::getWebsiteId();
	}
}