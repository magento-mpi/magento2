<?php

/**
 * Just a shortcut for popular constant :)
 */
define ('DS', DIRECTORY_SEPARATOR);

function __autoload($class)
{
    #echo "<hr>".$class;
    $classFile = str_replace(' ', DS, ucwords(str_replace('_', ' ', $class))).'.php';
    include ($classFile);
}

/**
 * Main Mage hub class
 *
 * @author Moshe Gurvich <moshe@varien.com>
 * @author Andrey Korolyov <andrey@varien.com>
 */
final class Mage {
    
    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry = array();
    
    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     */
    public static function register($key, $value)
    {
        self::$_registry[$key] = $value;
    }
    
    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     */
    public static function registry($key)
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }
        return null;
    }

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
            Mage::register('appRoot', $appRoot);
        } else {
            Mage::exception($appRoot.' is not a directory or not readable by this user');
        }
    }
    
    /**
     * Get application root absolute path
     *
     * @return string
     */
    
    public static function getRoot()
    {
        return Mage::registry('appRoot');
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     */
    public static function getBaseDir($type='', $moduleName='')
    {
        return Mage::getConfig()->getBaseDir($type, $moduleName);
    }


    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($type='', $moduleName='')
    {
        return Mage::getConfig()->getBaseUrl($type, $moduleName);
    }

    /**
     * Get a config object by module name
     *
     * @param string $moduleName
     * @return object
     */
    public static function getConfig($moduleName='Mage_Core')
    {
        $key = 'config_'.$moduleName;
        if (is_null(Mage::registry($key))) {
            $className = str_replace(' ', '_', ucwords(str_replace('_', ' ', $moduleName))).'_Config';
            Mage::register($key, new $className());
        }
        return Mage::registry($key);
    }

    /**
     * Retrieve event object
     *
     * @param string $name
     * @return Mage_Core_Event
     */
    public static function getEvent($name='')
    {
        return Mage::registry('events')->getEvent($name);
    }

    /**
     * Add event object
     *
     * @param unknown_type $name
     */
    public static function addEvent($name)
    {
        return Mage::registry('events')->addEvent($name);
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, $arguments=array(), $observerName='')
    {
        return Mage::registry('events')->addObserver($eventName, $callback, $arguments, $observerName);
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public static function addMultiObserver($eventRegex, $callback, $observerName='')
    {
        return Mage::registry('events')->addMultiObserver($eventRegex, $callback, $observerName);
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
        return Mage::registry('events')->dispatchEvent($name, $args);
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
        return Mage::registry('blocks')->createBlock($type, $name, $attributes);
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
        return Mage::registry('blocks')->getBlockByName($name);
    }

    /**
     * Get model class
     *
     * @link Mage_Core_Model::getResourceModelClass
     * @param string $model
     * @param string $class
     * @param array $arguments
     * @return Mage_Core_Model_Abstract
     */
    public static function getModel($model, $class='', array $arguments=array())
    {
        return Mage::getConfig()->getModelInstance($model, $class, $arguments);
    }
    
    public static function getSingleton($model, $class='', array $arguments=array())
    {
        $registryKey = '_singleton_'.$model.'_'.$class;
        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getModel($model, $class, $arguments));
        }
        return Mage::registry($registryKey);
    }

    /**
     * Get current website id on frontend
     *
     * @return integer
     */
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
    public static function exception($module='Mage_Core', $message='', $code=0)
    {
        $className = $module.'_Exception';
        return new $className($message, $code);
        //throw new $className($message, $code);
    }

    /**
     * Prepare folders and permissions
     *
     */
    public static function prepareFileSystem()
    {
        $xmlCacheDir = Mage::getBaseDir('var').DS.'cache'.DS.'config';
        if (!is_writable($xmlCacheDir)) {
            mkdir($xmlCacheDir, 0777, true);
        }
        $xmlCacheDir = Mage::getBaseDir('var').DS.'cache'.DS.'layout';
        if (!is_writable($xmlCacheDir)) {
            mkdir($xmlCacheDir, 0777, true);
        }
        $logDir =  Mage::getBaseDir('var').DS.'log';
        if (!is_writable($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $sessionDir =  Mage::getBaseDir('var').DS.'session';
        if (!is_writable($sessionDir)) {
            mkdir($sessionDir, 0777, true);
        }
    }

    /**
     * Initialize Mage
     *
     * @param string $appRoot
     */
    public static function init($appRoot='')
    {
        Varien_Profiler::setTimer('init');

        Mage::setRoot($appRoot);
        
        Mage::prepareFileSystem();
        
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();
        
        Mage::register('events', new Varien_Event());
        Mage::register('resources', new Mage_Core_Resource());

        Mage::getConfig()->init();

        Mage::register('session', Mage::getSingleton('core_model', 'session'));
        Mage::register('website', Mage::getSingleton('core_model', 'website'));
        Mage::register('blocks', Mage::getSingleton('core_model', 'block'));
        
        Varien_Profiler::setTimer('init', true);

        // check modules db
        Varien_Profiler::setTimer('applyDbUpdates');
        Mage_Core_Resource_Setup::applyAllUpdates();
        Varien_Profiler::setTimer('applyDbUpdates', true);
    }

    /**
     * Front end main entry point
     *
     * @param string $appRoot
     */
    public static function runFront($appRoot='')
    {
        try {
            Varien_Profiler::setTimer('totalApp');

            Mage::init($appRoot);
            Mage::getConfig()->loadEventObservers('front');

            Mage::register('controller', new Mage_Core_Controller_Zend_Front());
            Mage::registry('controller')->run();
            
            Varien_Profiler::setTimer('totalApp', true);
            
            /*
            echo '<hr><table border=1 align=center>';
            $timers = Varien_Profiler::getCumulativeTimer();
            foreach ($timers as $name=>$timer) echo '<tr><td>'.$name.'</td><td>'.number_format($timer[0],4).'</td></tr>';
            echo '</table>';
            */
            
            Varien_Profiler::getSqlProfiler(Mage::registry('resources')->getConnection('dev_write'));
            
        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }

    /**
     * Admin main entry point
     *
     * @param string $appRoot
     */
    public static function runAdmin($appRoot='')
    {
        try {
            Mage::init($appRoot);
        
            Mage::getConfig()->loadEventObservers('admin');
            
            Mage::register('controller', new Mage_Core_Controller_Zend_Admin());
            Mage::registry('controller')->run();

        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }

    /**
     * log facility (??)
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     */
    public static function log($message, $level=Zend_Log::LEVEL_DEBUG, $file = '')
    {
        if (empty($file)) {
            $file = 'system.log';
        }
        $logFile = Mage::getBaseDir('var').DS.'log'.DS.$file;

        if (!Zend_Log::hasLogger($file)) {
            Zend_Log::registerLogger(new Zend_Log_Adapter_File($logFile), $file);
        }
        
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message = date("Y-m-d H:i:s\t") . $message;
        
        Zend_Log::log($message, $level, $file);
    }
}

/**
 * Translator function
 * 
 * @param string $text the text to translate
 * @param mixed optional parameters to use in sprintf
 */
function __()
{
    $args = func_get_args();
    $text = array_shift($args);
    
    //translate $text
    
    array_unshift($args, $text);
    
    return call_user_func_array('sprintf', $args);
}
