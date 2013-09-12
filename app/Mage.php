<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Main Mage hub class
 */
final class Mage
{
    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';

    /**
     * Application initialization option to specify custom product edition label
     */
    const PARAM_EDITION = 'edition';

    /**
     * Application run code
     */
    const PARAM_RUN_CODE = 'MAGE_RUN_CODE';

    /**
     * Application run type (store|website)
     */
    const PARAM_RUN_TYPE = 'MAGE_RUN_TYPE';

    /**
     * Application run code
     */
    const PARAM_MODE = 'MAGE_MODE';

    /**
     * Base directory
     */
    const PARAM_BASEDIR = 'base_dir';

    /**
     * Custom application dirs
     */
    const PARAM_APP_DIRS = 'app_dirs';

    /**
     * Custom application uris
     */
    const PARAM_APP_URIS = 'app_uris';

    /**
     * Allowed modules
     */
    const PARAM_ALLOWED_MODULES = 'allowed_modules';

    /**
     * Caching params
     */
    const PARAM_CACHE_OPTIONS = 'cache_options';

    /**
     * Disallow cache
     */
    const PARAM_BAN_CACHE = 'global_ban_use_cache';

    /**
     * Custom local configuration file name
     */
    const PARAM_CUSTOM_LOCAL_FILE = 'custom_local_xml';

    /**
     * Custom local configuration
     */
    const PARAM_CUSTOM_LOCAL_CONFIG = 'custom_local_config';

    /**
     * Product edition labels
     */
    const EDITION_COMMUNITY    = 'Community';
    const EDITION_ENTERPRISE   = 'Enterprise';
    const EDITION_PROFESSIONAL = 'Professional';
    const EDITION_GO           = 'Go';

    /**
     * Default timezone
     */
    const DEFAULT_TIMEZONE  = 'UTC';

    /**
     * Magento version
     */
    const VERSION = '2.0.0.0-dev43';

    /**
     * Registry collection
     *
     * @var array
     */
    static private $_registry = array();

    /**
     * Application root absolute path
     *
     * @var string
     */
    static private $_appRoot;

    /**
     * Application model
     *
     * @var \Magento\Core\Model\App
     */
    static private $_app;

    /**
     * Config Model
     *
     * @var \Magento\Core\Model\Config
     */
    static private $_config;

    /**
     * Object manager interface
     *
     * @var \Magento\ObjectManager
     */
    static private $_objectManager;

    /**
     * Object cache instance
     *
     * @var \Magento\Object\Cache
     */
    static private $_objects;

    /**
     * Is downloader flag
     *
     * @var bool
     */
    static private $_isDownloader = false;

    /**
     * Is allow throw Exception about headers already sent
     *
     * @var bool
     */
    public static $headersSentThrowsException  = true;

    /**
     * Logger entities
     *
     * @var array
     */
    static private $_loggers = array();

    /**
     * Design object
     *
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected static $_design;

    /**
     * Current Magento edition.
     *
     * @var string
     * @static
     */
    static private $_currentEdition = self::EDITION_COMMUNITY;

    /**
     * Check if we need to use __sleep and __wakeup serialization methods in models
     *
     * @var bool
     */
    static private $_isSerializable = true;

    /**
     * Update mode flag
     *
     * @var bool
     */
    static private $_updateMode = false;

    /**
     * Gets the current Magento version string
     * @link http://www.magentocommerce.com/blog/new-community-edition-release-process/
     *
     * @return string
     */
    public static function getVersion()
    {
        $info = self::getVersionInfo();
        return trim("{$info['major']}.{$info['minor']}.{$info['revision']}"
            . ($info['patch'] != '' ? ".{$info['patch']}" : "")
            . "-{$info['stability']}{$info['number']}", '.-');
    }

    /**
     * Gets the detailed Magento version information
     * @link http://www.magentocommerce.com/blog/new-community-edition-release-process/
     *
     * @return array
     */
    public static function getVersionInfo()
    {
        return array(
            'major'     => '2',
            'minor'     => '0',
            'revision'  => '0',
            'patch'     => '0',
            'stability' => 'dev',
            'number'    => '45',
        );
    }

    /**
     * Get current Magento edition
     *
     * @static
     * @return string
     */
    public static function getEdition()
    {
        return self::$_currentEdition;
    }

    /**
     * Set edition
     *
     * @param string $edition
     */
    public static function setEdition($edition)
    {
        self::$_currentEdition = $edition;
    }

    /**
     * Set all my static data to defaults
     *
     */
    public static function reset()
    {
        self::resetRegistry();

        self::$_appRoot         = null;
        self::$_app             = null;
        self::$_config          = null;
        self::$_objects         = null;
        self::$_isDownloader    = false;
        self::$_loggers         = array();
        self::$_design          = null;
        self::$_objectManager   = null;
        // do not reset $headersSentThrowsException
    }

    /**
     * Reset registry
     */
    public static function resetRegistry()
    {
        foreach (array_keys(self::$_registry) as $key) {
            self::unregister($key);
        }

        self::$_registry = array();
    }

    /**
     * Register a new variable
     *
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws \Magento\Core\Exception
     *
     * @deprecated use \Magento\Core\Model\Registry::register
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(self::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            self::throwException('Mage registry key "' . $key . '" already exists');
        }
        self::$_registry[$key] = $value;
    }

    /**
     * Unregister a variable from register by key
     *
     * @param string $key
     *
     * @deprecated use \Magento\Core\Model\Registry::unregister
     */
    public static function unregister($key)
    {
        if (isset(self::$_registry[$key])) {
            if (is_object(self::$_registry[$key]) && (method_exists(self::$_registry[$key], '__destruct'))) {
                self::$_registry[$key]->__destruct();
            }
            unset(self::$_registry[$key]);
        }
    }

    /**
     * Retrieve a value from registry by a key
     *
     * @param string $key
     * @return mixed
     *
     * @deprecated use \Magento\Core\Model\Registry::registry
     */
    public static function registry($key)
    {
        if (isset(self::$_registry[$key])) {
            return self::$_registry[$key];
        }
        return null;
    }

    /**
     * Retrieve application root absolute path
     *
     * @return string
     * @throws \Magento\Exception
     */
    public static function getRoot()
    {
        if (!self::$_appRoot) {
            $appRootDir = __DIR__;
            if (!is_readable($appRootDir)) {
                throw new \Magento\Exception("Application root directory '$appRootDir' is not readable.");
            }
            self::$_appRoot = $appRootDir;
        }
        return self::$_appRoot;
    }

    /**
     * Magento Objects Cache
     *
     * @param string $key optional, if specified will load this key
     * @return \Magento\Object\Cache
     */
    public static function objects($key = null)
    {
        if (!self::$_objects) {
            self::$_objects = new Magento\Object\Cache;
        }
        if (is_null($key)) {
            return self::$_objects;
        } else {
            return self::$_objects->load($key);
        }
    }

    /**
     * Retrieve application root absolute path
     *
     * @param string $type
     * @return string
     */
    public static function getBaseDir($type = \Magento\Core\Model\Dir::ROOT)
    {
        return self::getSingleton('Magento\Core\Model\Dir')->getDir($type);
    }

    /**
     * Retrieve module absolute path by directory type
     *
     * @param string $type
     * @param string $moduleName
     * @return string
     */
    public static function getModuleDir($type, $moduleName)
    {
        return self::getObjectManager()->get('Magento\Core\Model\Config\Modules\Reader')->getModuleDir($type, $moduleName);
    }

    /**
     * Retrieve config value for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public static function getStoreConfig($path, $store = null)
    {
        return self::app()->getStore($store)->getConfig($path);
    }

    /**
     * Retrieve config flag for store by path
     *
     * @param string $path
     * @param mixed $store
     * @return bool
     */
    public static function getStoreConfigFlag($path, $store = null)
    {
        $flag = strtolower(self::getStoreConfig($path, $store));
        if (!empty($flag) && 'false' !== $flag) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get base URL path by type
     *
     * @param string $type
     * @param null|bool $secure
     * @return string
     */
    public static function getBaseUrl($type = \Magento\Core\Model\Store::URL_TYPE_LINK, $secure = null)
    {
        return self::app()->getStore()->getBaseUrl($type, $secure);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public static function getUrl($route = '', $params = array())
    {
        return self::getObjectManager()->create('Magento\Core\Model\Url')->getUrl($route, $params);
    }

    /**
     * Retrieve a config instance
     *
     * This method doesn't suit Magento 2 anymore, it is left only until refactoring, when all calls
     * to Mage::getConfig() will be removed in favor of config dependency injection.
     *
     * @return \Magento\Core\Model\Config
     */
    public static function getConfig()
    {
        if (!self::$_config) {
            self::$_config = self::getObjectManager()->get('Magento\Core\Model\Config');
        }
        return self::$_config;
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiple observers matching event name pattern
     *
     * @param string $name
     * @param array $data
     *
     * @deprecated use \Magento\Core\Model\Event\Manager::dispatch
     */
    public static function dispatchEvent($name, array $data = array())
    {
        return Mage::getSingleton('Magento\Core\Model\Event\Manager')->dispatch($name, $data);
    }

    /**
     * Retrieve model object
     *
     * @param   string $modelClass
     * @param   array|object $arguments
     * @return  \Magento\Core\Model\AbstractModel|false
     */
    public static function getModel($modelClass = '', $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }
        return self::getObjectManager()->create($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @param   string $modelClass
     * @return  \Magento\Core\Model\AbstractModel
     */
    public static function getSingleton($modelClass = '')
    {
        $registryKey = '_singleton/' . $modelClass;
        if (!self::registry($registryKey)) {
            self::register($registryKey, self::getObjectManager()->get($modelClass));
        }
        return self::registry($registryKey);
    }

    /**
     * Retrieve object manager
     *
     * @static
     * @return \Magento\ObjectManager
     */
    public static function getObjectManager()
    {
        return self::$_objectManager;
    }

    /**
     * Set application object manager
     *
     * @param \Magento\ObjectManager $objectManager
     * @throws LogicException
     */
    public static function setObjectManager(\Magento\ObjectManager $objectManager)
    {
        if (!self::$_objectManager) {
            self::$_objectManager = $objectManager;
        } else {
            throw new LogicException('Only one object manager can be used in application');
        }
    }

    /**
     * Retrieve object of resource model
     *
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Object
     */
    public static function getResourceModel($modelClass, $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }
        return self::getObjectManager()->create($modelClass, $arguments);
    }

    /**
     * Retrieve resource model object singleton
     *
     * @param   string $modelClass
     * @return  object
     */
    public static function getResourceSingleton($modelClass = '')
    {
        $registryKey = '_resource_singleton/' . $modelClass;
        if (!self::registry($registryKey)) {
            self::register($registryKey, self::getObjectManager()->get($modelClass));
        }
        return self::registry($registryKey);
    }

    /**
     * Returns block singleton instance, if current action exists. Otherwise returns FALSE.
     *
     * @param string $className
     * @return mixed
     */
    public static function getBlockSingleton($className)
    {
        $action = self::app()->getFrontController()->getAction();
        return $action ? $action->getLayout()->getBlockSingleton($className) : false;
    }

    /**
     * Retrieve helper object
     *
     * @param string $name the helper name
     * @return \Magento\Core\Helper\AbstractHelper
     */
    public static function helper($name)
    {
        /* Default helper class for a module */
        if (strpos($name, '\\Helper\\') === false) {
            $name .= '\\Helper\\Data';
        }

        $registryKey = '_helper/' . $name;
        if (!self::registry($registryKey)) {
            self::register($registryKey, self::getObjectManager()->get($name));
        }
        return self::registry($registryKey);
    }

    /**
     * Retrieve resource helper object
     *
     * @param string $moduleName
     * @return \Magento\Core\Model\Resource\Helper\AbstractHelper
     */
    public static function getResourceHelper($moduleName)
    {
        $connectionModel = self::getObjectManager()
            ->get('Magento\Core\Model\Config\Resource')
            ->getResourceConnectionModel('core');

        $helperClassName = str_replace('_', '\\', $moduleName) . '\\Model\\Resource\\Helper\\'
            . ucfirst($connectionModel);
        $connection = strtolower($moduleName);
        if (substr($moduleName, 0, 8) == 'Magento_') {
            $connection = substr($connection, 8);
        }
        $key = 'resourceHelper/' . $connection;
        if (!self::registry($key)) {
            self::register(
                $key, self::getObjectManager()->create($helperClassName, array('modulePrefix' => $connection))
            );
        }
        return self::registry($key);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @param string $module
     * @param string $message
     * @param integer $code
     * @return \Magento\Core\Exception
     */
    public static function exception($module = 'Magento_Core', $message = '', $code = 0)
    {
        $module = str_replace('_', \Magento\Autoload\IncludePath::NS_SEPARATOR, $module);
        $className = $module . \Magento\Autoload\IncludePath::NS_SEPARATOR . 'Exception';
        return new $className($message, $code);
    }

    /**
     * Throw Exception
     *
     * @param string $message
     * @param string $messageStorage
     * @throws \Magento\Core\Exception
     */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new \Magento\Core\Exception($message);
    }

    /**
     * Get application object.
     *
     * @return \Magento\Core\Model\App
     */
    public static function app()
    {
        if (null === self::$_app) {
            self::$_app = self::getObjectManager()->get('Magento\Core\Model\App');
        }
        return self::$_app;
    }

    /**
     * Check if application is installed
     *
     * @return bool
     * @deprecated use \Magento\Core\Model\App\State::isInstalled()
     */
    public static function isInstalled()
    {
        return (bool) Mage::getSingleton('Magento\Core\Model\Config\Primary')->getInstallDate();
    }

    /**
     * log a message to system log or arbitrary file
     *
     * @param string $message
     * @param integer $level
     * @param string $file
     * @param bool $forceLog
     */
    public static function log($message, $level = null, $file = 'system.log', $forceLog = false)
    {
        $level = is_null($level) ? Zend_Log::DEBUG : $level;
        if (empty($file) || $file == 'system.log') {
            $file = 'system.log';
            $key = \Magento\Core\Model\Logger::LOGGER_SYSTEM;
        } elseif ($file == 'exception.log') {
            $key = \Magento\Core\Model\Logger::LOGGER_EXCEPTION;
        } else {
            $forceLog = true;
            $key = $file;
        }
        /** @var $logger \Magento\Core\Model\Logger */
        $logger = self::$_objectManager->get('Magento\Core\Model\Logger');
        if ($forceLog && !$logger->hasLog($key)) {
            $logger->addStreamLog($key, $file);
        }
        $logger->log($message, $level, $key);
    }

    /**
     * Write exception to log
     *
     * @param Exception $exception
     */
    public static function logException(Exception $exception)
    {
        self::$_objectManager->get('Magento\Core\Model\Logger')->logException($exception);
    }

    /**
     * Retrieve enabled developer mode
     *
     * @return bool
     * @deprecated use \Magento\Core\Model\App\State::getMode()
     */
    public static function getIsDeveloperMode()
    {
        $objectManager = self::getObjectManager();
        if (!$objectManager) {
            return false;
        }

        $appState = $objectManager->get('Magento\Core\Model\App\State');
        if (!$appState) {
            return false;
        }

        $mode = $appState->getMode();
        return $mode == \Magento\Core\Model\App\State::MODE_DEVELOPER;
    }

    /**
     * Display exception
     *
     * @param Exception $e
     * @param string $extra
     */
    public static function printException(Exception $e, $extra = '')
    {
        if (self::getIsDeveloperMode()) {
            print '<pre>';

            if (!empty($extra)) {
                print $extra . "\n\n";
            }

            print $e->getMessage() . "\n\n";
            print $e->getTraceAsString();
            print '</pre>';
        } else {

            $reportData = array(
                !empty($extra) ? $extra . "\n\n" : '' . $e->getMessage(),
                $e->getTraceAsString()
            );

            // retrieve server data
            if (isset($_SERVER)) {
                if (isset($_SERVER['REQUEST_URI'])) {
                    $reportData['url'] = $_SERVER['REQUEST_URI'];
                }
                if (isset($_SERVER['SCRIPT_NAME'])) {
                    $reportData['script_name'] = $_SERVER['SCRIPT_NAME'];
                }
            }

            // attempt to specify store as a skin
            try {
                $storeCode = self::app()->getStore()->getCode();
                $reportData['skin'] = $storeCode;
            } catch (Exception $e) {
            }

            require_once(self::getBaseDir(\Magento\Core\Model\Dir::PUB) . DS . 'errors' . DS . 'report.php');
        }

        die();
    }

    /**
     * Set is downloader flag
     *
     * @param bool $flag
     *
     * @deprecated use \Magento\Core\Model\App\State::setIsDownloader()
     */
    public static function setIsDownloader($flag = true)
    {
        self::$_isDownloader = $flag;
    }

    /**
     * Set is serializable flag
     *
     * @static
     * @param bool $value
     *
     * @deprecated use \Magento\Core\Model\App\State::setIsSerializable()
     */
    public static function setIsSerializable($value = true)
    {
        self::$_isSerializable = !empty($value);
    }

    /**
     * Get is serializable flag
     *
     * @static
     * @return bool
     *
     * @deprecated use \Magento\Core\Model\App\State::getIsSerializable()
     */
    public static function getIsSerializable()
    {
        return self::$_isSerializable;
    }

    /**
     * Set update mode flag
     *
     * @param bool $value
     *
     * @deprecated use \Magento\Core\Model\App\State::setUpdateMode()
     */
    public static function setUpdateMode($value)
    {
        self::$_updateMode = $value;

    }

    /**
     * Get update mode flag
     * @return bool
     *
     * @deprecated use \Magento\Core\Model\App\State::setUpdateMode()
     */
    public static function getUpdateMode()
    {
        return self::$_updateMode;
    }
}
