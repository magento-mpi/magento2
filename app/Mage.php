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
    /**
     * Is allow throw Exception about headers already sent
     *
     * @var bool
     */
    public static $headersSentThrowsException  = true;

    /**
     * Retrieve application root absolute path
     *
     * @deprecated
     * @param string $type
     * @return string
     */
    public static function getBaseDir($type = Magento_Core_Model_Dir::ROOT)
    {
        return self::getSingleton('Magento_Core_Model_Dir')->getDir($type);
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
        return Magento_Core_Model_ObjectManager::getInstance()
            ->get('Magento_Core_Model_Config_Modules_Reader')
            ->getModuleDir($type, $moduleName);
    }

    /**
     * Get base URL path by type
     *
     * @deprecated
     * @param string $type
     * @param null|bool $secure
     * @return string
     */
    public static function getBaseUrl($type = Magento_Core_Model_Store::URL_TYPE_LINK, $secure = null)
    {
        return self::app()->getStore()->getBaseUrl($type, $secure);
    }

    /**
     * Generate url by route and parameters
     *
     * @deprecated
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public static function getUrl($route = '', $params = array())
    {
        return Magento_Core_Model_ObjectManager::getInstance()
            ->create('Magento_Core_Model_Url')
            ->getUrl($route, $params);
    }

    /**
     * Retrieve model object
     *
     * @deprecated
     * @param   string $modelClass
     * @param   array|object $arguments
     * @return  Magento_Core_Model_Abstract|false
     */
    public static function getModel($modelClass = '', $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }
        return Magento_Core_Model_ObjectManager::getInstance()->create($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @deprecated
     * @param   string $modelClass
     * @return  Magento_Core_Model_Abstract
     */
    public static function getSingleton($modelClass = '')
    {
        $registryKey = '_singleton/' . $modelClass;
        $objectManager = Magento_Core_Model_ObjectManager::getInstance();
        /** @var Magento_Core_Model_Registry $registryObject */
        $registryObject = $objectManager->get('Magento_Core_Model_Registry');
        if (!$registryObject->registry($registryKey)) {
            $registryObject->register($registryKey, $objectManager->get($modelClass));
        }
        return $registryObject->registry($registryKey);
    }

    /**
     * Retrieve object of resource model
     *
     * @deprecated
     * @param   string $modelClass
     * @param   array $arguments
     * @return  Object
     */
    public static function getResourceModel($modelClass, $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }
        return Magento_Core_Model_ObjectManager::getInstance()->create($modelClass, $arguments);
    }

    /**
     * Retrieve resource model object singleton
     *
     * @deprecated
     * @param   string $modelClass
     * @return  object
     */
    public static function getResourceSingleton($modelClass = '')
    {
        $objectManager = Magento_Core_Model_ObjectManager::getInstance();
        /** @var Magento_Core_Model_Registry $registryObject */
        $registryObject = $objectManager->get('Magento_Core_Model_Registry');
        $registryKey = '_resource_singleton/' . $modelClass;
        if (!$registryObject->registry($registryKey)) {
            $registryObject->register($registryKey, $objectManager->get($modelClass));
        }
        return $registryObject->registry($registryKey);
    }

    /**
     * Returns block singleton instance, if current action exists. Otherwise returns FALSE.
     *
     * @deprecated
     * @param string $className
     * @return mixed
     */
    public static function getBlockSingleton($className)
    {
        $action = self::app()->getFrontController()->getAction();
        return $action ? $action->getLayout()->getBlockSingleton($className) : false;
    }

    /**
     * Retrieve resource helper object
     *
     * @deprecated
     * @param string $moduleName
     * @return Magento_Core_Model_Resource_Helper_Abstract
     */
    public static function getResourceHelper($moduleName)
    {
        return Magento_Core_Model_ObjectManager::getInstance()->get('Magento_Core_Model_Resource_HelperPool')
            ->get($moduleName);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @deprecated
     * @param string $module
     * @param string $message
     * @param integer $code
     * @return Magento_Core_Exception
     */
    public static function exception($module = 'Magento_Core', $message = '', $code = 0)
    {
        $className = $module . '_Exception';
        return new $className($message, $code);
    }

    /**
     * Throw Exception
     *
     * @deprecated
     * @param string $message
     * @param string $messageStorage
     * @throws Magento_Core_Exception
     */
    public static function throwException($message, $messageStorage = null)
    {
        if ($messageStorage && ($storage = self::getSingleton($messageStorage))) {
            $storage->addError($message);
        }
        throw new Magento_Core_Exception($message);
    }

    /**
     * Get application object.
     *
     * @return Magento_Core_Model_App
     * @deprecated
     */
    public static function app()
    {
        return Magento_Core_Model_ObjectManager::getInstance()->get('Magento_Core_Model_App');
    }

    /**
     * Check if application is installed
     *
     * @return bool
     * @deprecated use Magento_Core_Model_App_State::isInstalled()
     */
    public static function isInstalled()
    {
        return (bool) Mage::getSingleton('Magento_Core_Model_App_State')->isInstalled();
    }

    /**
     * Retrieve enabled developer mode
     *
     * @return bool
     * @deprecated use Magento_Core_Model_App_State::getMode()
     */
    public static function getIsDeveloperMode()
    {
        $objectManager = Magento_Core_Model_ObjectManager::getInstance();
        if (!$objectManager) {
            return false;
        }

        $appState = $objectManager->get('Magento_Core_Model_App_State');
        if (!$appState) {
            return false;
        }

        $mode = $appState->getMode();
        return $mode == Magento_Core_Model_App_State::MODE_DEVELOPER;
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

            require_once(self::getBaseDir(Magento_Core_Model_Dir::PUB) . DS . 'errors' . DS . 'report.php');
        }

        die();
    }
}
