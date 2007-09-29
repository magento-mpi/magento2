<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BP', dirname(dirname(__FILE__)));

/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);

/**
 * Include path
 */
ini_set('include_path', ini_get('include_path')
    .PS.BP.'/lib'
    .PS.BP.'/app/code/core'
    .PS.BP.'/app/code/local'
    .PS.BP.'/app/code/community'
#    .PS.BP.'/var/cache/code'
);

include_once "Mage/Core/functions.php";
include_once "Varien/Profiler.php";

Varien_Profiler::enable();
/*
Varien_Profiler::start('pre-includes');
include_once "Core.php";
include_once "Eav.php";
include_once "Page.php";
include_once "Rule.php";
include_once "Directory.php";
include_once "SalesRule.php";
include_once "Cms.php";
include_once "Log.php";
include_once "Install.php";
include_once "Customer.php";
include_once "Catalog.php";
include_once "Checkout.php";
include_once "CatalogRule.php";
include_once "GoogleAnalytics.php";
Varien_Profiler::stop('pre-includes');
*/

/**
 * Check magic quotes settings
 */
checkMagicQuotes();

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
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    static private $_app;

    static private $_useCache = array();

    public static function getVersion()
    {
        return '0.6.12383';
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     */
    public static function register($key, $value)
    {
        // why? - moshe
        // We can not have 2 object for one key - dmitriy
        if(isset(self::$_registry[$key])){
            Mage::throwException('Mage registry key "'.$key.'" already exists');
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
        //Mage::throwException('Mage registry key "'.$key.'" do not exists');
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
            Mage::throwException($appRoot.' is not a directory or not readable by this user');
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

    public static function useCache($type, $flag=null)
    {
        $value = isset(self::$_useCache[$type]) ? self::$_useCache[$type] : false;
        if (!is_null($flag)) {
            self::$_useCache[$type] = $flag;
        }
        return $value;
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

    public static function getStoreConfig($path, $id=null)
    {
    	if(!self::getConfig()->getIsInstalled()) {
    		$id = null;
    	}

        if (empty($id)) {
            $store = Mage::app()->getStore();
        } elseif (is_numeric($id)) {
            $store = Mage::getModel('core/store')->load($id);
            if (!$store->getCode()) {
                throw Mage::exception('Mage_Core', 'Invalid store id requested: '.$id);
            }
        } elseif (is_string($id)) {
            $storeConfig = Mage::getConfig()->getNode('stores/'.$id);
            if (!$storeConfig) {
                throw Mage::exception('Mage_Core', 'Invalid store code requested: '.$id);
            }
            $store = Mage::getModel('core/store')->setCode($id);
        } else {
            throw Mage::exception('Mage_Core', 'Invalid store id requested: '.$id);
        }

        return $store->getConfig($path);
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @return string
     */
    public static function getBaseUrl($params=array())
    {
        return Mage::app()->getStore()->getUrl($params);
    }

    public static function getUrl($route='', $params=array())
    {
        return Mage::registry('controller')->getUrl($route, $params);
    }

    /**
     * Get design package singleton
     *
     * @return Mage_Core_Model_Design_Package
     */
    public static function getDesign()
    {
        return Mage::getSingleton('core/design_package');
    }

    /**
     * Get a config object
     *
     * @return Mage_Core_Model_Config
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
    public static function addObserver($eventName, $callback, $data=array(), $observerName='', $observerClass='')
    {
        if ($observerClass=='') {
            $observerClass = 'Varien_Event_Observer';
        }
        $observer = new $observerClass();
        $observer->setName($observerName)->addData($data)->setEventName($eventName)->setCallback($callback);
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
        $result = Mage::registry('events')->dispatch($name, $data);
        return $result;
    }

    /**
     * Get model class
     *
     * @link Mage_Core_Model_Config::getModelInstance
     * @param string $modelClass
     * @param array $arguments
     * @return Varien_Object
     */
    public static function getModel($modelClass='', $arguments=array())
    {
        return Mage::getConfig()->getModelInstance($modelClass, $arguments);
    }

    public static function getSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_singleton/'.$modelClass;
        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getModel($modelClass, $arguments));
        }
        return Mage::registry($registryKey);
    }

    /**
     * Get resource model class
     *
     * @param string $modelClass
     * @param array $arguments
     * @return Object
     */
    public static function getResourceModel($modelClass, $arguments=array())
    {
        return Mage::getConfig()->getResourceModelInstance($modelClass, $arguments);
    }

    public static function getResourceSingleton($modelClass='', array $arguments=array())
    {
        $registryKey = '_resource_singleton/'.$modelClass;
        if (!Mage::registry($registryKey)) {
            Mage::register($registryKey, Mage::getResourceModel($modelClass, $arguments));
        }
        return Mage::registry($registryKey);
    }

    public static function getHelper($type)
    {
        return Mage::registry('action')->getLayout()->getHelper($type);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param integer $code
     */
    public static function exception($module='Mage_Core', $message='', $code=0)
    {
        $className = $module.'_Exception';
        return new $className($message, $code);
        //throw new $className($message, $code);
    }

    public static function throwException($message, $mesageStorage=null)
    {
        if ($mesageStorage && ($storage = Mage::getSingleton($mesageStorage))) {
            $storage->addError($message);
        }
        throw new Exception($message);
    }

    public static function currency($value, $format=true)
    {
        try {
            $value = Mage::app()->getStore()->convertPrice($value, $format);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }
    	return $value;
    }

    /**
     * Initialize and retrieve application
     *
     * @param   string $store
     * @param   string $etcDir
     * @return  Mage_Core_Model_App
     */
    public static function app($store='', $etcDir='')
    {
        if (is_null(self::$_app)) {
            Mage::setRoot();
            Mage::register('events', new Varien_Event_Collection());
            Mage::register('config', new Mage_Core_Model_Config());

            self::$_app = new Mage_Core_Model_App($store, $etcDir);
            self::$_app->loadAreaPart(Mage_Core_Model_App_Area::AREA_GLOBAL, Mage_Core_Model_App_Area::PART_EVENTS);
        }
        return self::$_app;
    }

    /**
     * Front end main entry point
     *
     * @param string $storeCode
     */
    public static function run($storeCode='', $etcDir=null)
    {
        self::log('===================== START ==========================');

        try {
            Varien_Profiler::start('app');

            self::app($storeCode, $etcDir);
            self::app()->getFrontController()->dispatch();

            Varien_Profiler::stop('app');
        }
        catch (Exception $e) {
            echo $e;
            if (self::app()->isInstalled()) {
                self::printException($e);
                exit();
            }
            try {
                self::dispatchEvent('mageRunException', array('exception'=>$e));
                if (!headers_sent()) {
                	header('Location:'.Mage::getBaseUrl().'install/');
                }
                else {
                    self::printException($e);
                }
            }
            catch (Exception $ne){
                self::printException($e);
                self::printException($ne);
            }
        }

        self::log('===================== FINISH ==========================');
    }

    public static function cron($etcDir=null)
    {
        Mage::init($etcDir);
        Mage::getConfig()->loadEventObservers('crontab');
        Mage::dispatchEvent('crontab');
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
		return;

        static $loggers = array();

        if (empty($file)) {
            $file = 'system.log';
        }

        try {
            if (empty($loggers[$file])) {
                $logFile = Mage::getBaseDir('var').DS.'log'.DS.$file;
                $logDir = Mage::getBaseDir('var').DS.'log';

                if (!is_dir(Mage::getBaseDir('var').DS.'log')) {
                    mkdir(Mage::getBaseDir('var').DS.'log', 0777);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile,'');
                    chmod($logFile, 0777);
                }

                $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                $formatter = new Zend_Log_Formatter_Simple($format);
                $writer = new Zend_Log_Writer_Stream($logFile);
                $writer->setFormatter($formatter);
                $loggers[$file] = new Zend_Log($writer);
            }

            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }

            $loggers[$file]->log($message, $level);
        }
        catch (Exception $e){

        }
    }

    /**
     * Display exception
     *
     * @param Exception $e
     */
    public static function printException(Exception $e)
    {
        echo '<pre>';
        echo ($e);
        echo '</pre>';
    }
}
