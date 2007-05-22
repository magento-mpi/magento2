<?php

/**
 * Just a shortcut for popular constant :)
 */
define ('DS', DIRECTORY_SEPARATOR);

function __autoload($class)
{
    #echo "<hr>".$class;
    $classFile = uc_words($class, DS).'.php';
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
        if(isset(self::$_registry[$key])){
            throw new Exception('Mage registry key "'.$key.'" already exists');
        }
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
    public static function getBaseDir($type='')
    {
        return Mage::getConfig()->getBaseDir($type);
    }
    
    public static function getModuleDir($type, $moduleName)
    {
        return Mage::getConfig()->getModuleDir($type, $moduleName);
    }

    public static function getWebsiteDir($type, $websiteCode=null)
    {
        if (is_null($websiteCode)) {
            $website = Mage::registry('website');
        } else {
            $website = Mage::getModel('core', 'website')->setCode($websiteCode);
        }
        return $website->getDir($type);
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($params=array())
    {
        return Mage::registry('website')->getUrl($params);
    }
    
    public static function getUrl($routeName='', $params=array())
    {
        return Mage::getConfig()->getRouterInstance($routeName)->getUrl($params);
    }

    /**
     * Get a config object by module name
     *
     * @param string $moduleName
     * @return object
     */
    public static function getConfig()
    {
        return Mage::registry('config');
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, $data=array(), $observerName='')
    {
        $observer = new Varien_Event_Observer();
        $observer->addData($data)->setName($observerName)->setEventName($eventName)->setCallback($callback);
        return Mage::registry('events')->addObserver($observer);
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
    public static function dispatchEvent($name, array $data=array())
    {
        return Mage::registry('events')->dispatch($name, $data);
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
        return Mage::registry('controller')->getLayout()->createBlock($type, $name, $attributes);
    }
    
    /**
     * Return Block object for block id
     *
     * @link    Mage_Core_Block::getBlock
     * @param   string $id
     * @return  Mage_Core_Block_Abstract
     */
    public static function getBlock($name)
    {
        return Mage::registry('controller')->getLayout()->getBlock($name);
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
    public static function getModel($model, $class='', $arguments=array())
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
     * Initialize Mage
     *
     * @param string $appRoot
      */
    public static function init($appRoot='')
    {
        Varien_Profiler::setTimer('init');
        
        date_default_timezone_set('America/Los_Angeles');

        Mage::setRoot($appRoot);
        
        Mage::register('events', new Varien_Event_Collection());
        Mage::register('config', new Mage_Core_Model_Config());
        Mage::register('resources', new Mage_Core_Model_Resource());

        Varien_Profiler::setTimer('config');
        Mage::getConfig()->init();
        Varien_Profiler::setTimer('config', true);

        // Session must be start after init resources
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('session')));
        Zend_Session::start();

        Mage::register('session', Mage::getSingleton('core', 'session'));
        Mage::register('website', Mage::getSingleton('core', 'website'));
        
        Varien_Profiler::setTimer('init', true);

        // check modules db
        Varien_Profiler::setTimer('applyDbUpdates');
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        Varien_Profiler::setTimer('applyDbUpdates', true);
    }

    /**
     * Front end main entry point
     *
     * @param string $appRoot
     */
    public static function runFront($websiteCode)
    {
        try {
            Varien_Profiler::setTimer('totalApp');

            Mage::init();
            Mage::getConfig()->loadEventObservers('front');
            
            Mage::registry('website')->setCode($websiteCode);

            Mage::register('controller', new Mage_Core_Controller_Zend_Front());
            Mage::registry('controller')->run();
            
            Varien_Profiler::setTimer('totalApp', true);
            
            
            /*echo '<hr><table border=1 align=center>';
            $timers = Varien_Profiler::getCumulativeTimer();
            foreach ($timers as $name=>$timer) echo '<tr><td>'.$name.'</td><td>'.number_format($timer[0],4).'</td></tr>';
            echo '</table>';*/
            
            
            Varien_Profiler::getSqlProfiler(Mage::registry('resources')->getConnection('dev_write'));
            
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
    public static function log($message, $level=Zend_Log::DEBUG, $file = '')
    {
        static $loggers = array();
        
        if (empty($file)) {
            $file = 'system.log';
        }

        if (empty($loggers[$file])) {
            $logFile = Mage::getBaseDir('var').DS.'log'.DS.$file;
            $writer = new Zend_Log_Writer_Stream($logFile);
            $loggers[$file] = new Zend_Log($writer);
        }
        
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message = date("Y-m-d H:i:s\t") . $message;
        
        $loggers[$file]->log($message, $level);
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
    return Mage::getSingleton('core', 'translate')->translate($args);
    /*$text = array_shift($args);
    
    //translate $text
    
    array_unshift($args, $text);
    
    return call_user_func_array('sprintf', $args);*/
}

/**
 * Tiny function to enhance functionality of ucwords
 * 
 * Will capitalize first letters and convert separators if needed
 *
 * @param string $str
 * @param string $destSep
 * @param string $srcSep
 * @return string
 */
function uc_words($str, $destSep='_', $srcSep='_')
{
    return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
}
