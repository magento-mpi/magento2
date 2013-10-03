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
     * Retrieve application root absolute path
     *
     * @deprecated
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
        return \Magento\Core\Model\ObjectManager::getInstance()
            ->get('Magento\Core\Model\Config\Modules\Reader')
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
    public static function getBaseUrl($type = \Magento\Core\Model\Store::URL_TYPE_LINK, $secure = null)
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
        return \Magento\Core\Model\ObjectManager::getInstance()
            ->create('Magento\Core\Model\Url')
            ->getUrl($route, $params);
    }

    /**
     * Retrieve model object
     *
     * @deprecated
     * @param   string $modelClass
     * @param   array|object $arguments
     * @return  \Magento\Core\Model\AbstractModel|false
     */
    public static function getModel($modelClass = '', $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments);
        }
        return \Magento\Core\Model\ObjectManager::getInstance()->create($modelClass, $arguments);
    }

    /**
     * Retrieve model object singleton
     *
     * @deprecated
     * @param   string $modelClass
     * @return  \Magento\Core\Model\AbstractModel
     */
    public static function getSingleton($modelClass = '')
    {
        $registryKey = '_singleton/' . $modelClass;
        $objectManager = \Magento\Core\Model\ObjectManager::getInstance();
        /** @var \Magento\Core\Model\Registry $registryObject */
        $registryObject = $objectManager->get('Magento\Core\Model\Registry');
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
        return \Magento\Core\Model\ObjectManager::getInstance()->create($modelClass, $arguments);
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
        $objectManager = \Magento\Core\Model\ObjectManager::getInstance();
        /** @var \Magento\Core\Model\Registry $registryObject */
        $registryObject = $objectManager->get('Magento\Core\Model\Registry');
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
     * @return \Magento\Core\Model\Resource\Helper\AbstractHelper
     */
    public static function getResourceHelper($moduleName)
    {
        return \Magento\Core\Model\ObjectManager::getInstance()->get('Magento\Core\Model\Resource\HelperPool')
            ->get($moduleName);
    }

    /**
     * Return new exception by module to be thrown
     *
     * @deprecated
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
     * Throw \Exception
     *
     * @deprecated
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
     * @deprecated
     */
    public static function app()
    {
        return \Magento\Core\Model\ObjectManager::getInstance()->get('Magento\Core\Model\App');
    }

    /**
     * Check if application is installed
     *
     * @return bool
     * @deprecated use \Magento\Core\Model\App\State::isInstalled()
     */
    public static function isInstalled()
    {
        return (bool) \Mage::getSingleton('Magento\Core\Model\App\State')->isInstalled();
    }

    /**
     * Retrieve enabled developer mode
     *
     * @return bool
     * @deprecated use \Magento\Core\Model\App\State::getMode()
     */
    public static function getIsDeveloperMode()
    {
        $objectManager = \Magento\Core\Model\ObjectManager::getInstance();
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
}
