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
    
    static private $_registry = array();
    
    public static function register($key, $value)
    {
        self::$_registry[$key] = $value;
    }
    
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

    public static function getConfig($param='')
    {
        if (is_null(Mage::registry('config'))) {
            Mage::register('config', new Mage_Core_Config());
        }
        if (''==$param) {
            return Mage::registry('config');
        } elseif ('/'===$param) {
            return Mage::registry('config')->getXml();
        } else {
            return Mage::registry('config')->getXpath($param);
        }
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
    }

    /**
     * Initialize Mage
     *
     * @param string $appRoot
     */
    public static function init($appRoot='')
    {
        Varien_Profiler::setTimer('app');

        Mage::setRoot($appRoot);
        
        Mage::getConfig()->init();

        Mage::prepareFileSystem();

        Mage::register('events', new Varien_Event());
        
        // check modules db
        #Mage::getConfig()->applyDbUpdates();
        #echo Varien_Profiler::setTimer('app').',';
    }

    /**
     * Mage main entry point
     *
     * @param string $appRoot
     */
    public static function runFront($appRoot='')
    {
        try {
            Mage::init($appRoot);

            Mage::getConfig()->loadEventObservers('front');

            #Varien_Profiler::setTimer('zend_controller');
            Mage_Core_Controller::setController(new Mage_Core_Controller_Zend_Front());
            #Varien_Profiler::setTimer('zend_controller', true);
            Mage_Core_Controller::getController()->run();

            Varien_Profiler::getTimer('app', true);
            
            Varien_Profiler::getSqlProfiler(Mage_Core_Resource::getResource('dev_write')->getConnection());
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
            Mage::init($appRoot);
        
            Mage::getConfig()->loadEventObservers('admin');
            
            Mage_Core_Controller::setController(new Mage_Core_Controller_Zend_Admin());
            Mage_Core_Controller::getController()->run();

            //Varien_Profiler::getTimer('app', true);
            //  Varien_Profiler::getSqlProfiler();
        } catch (Zend_Exception $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        } catch (PDOException $e) {
            echo $e->getMessage()."<pre>".$e->getTraceAsString();
        }
    }

    public static function test()
    {
        Mage::init();

        Varien_Profiler::setTimer('config');
        Mage::register('config', new Mage_Core_Config());
        echo Varien_Profiler::setTimer('config');

        echo "<xmp>TEST:";
        print_r(Mage::getConfig());
    }
    
    public static function log($message, $level=Zend_Log::LEVEL_DEBUG, $file = '')
    {
        if (empty($file)) {
            $file = 'system.log';
        }
        $logFile = Mage::getRoot('var').DS.'log'.DS.$file;
        
        if (!Zend_Log::hasLogger($file)) {
            Zend_Log::registerLogger(new Zend_Log_Adapter_File($logFile), $file);
        }
        
        Zend_Log::log($message, $level, $file);
    }
}