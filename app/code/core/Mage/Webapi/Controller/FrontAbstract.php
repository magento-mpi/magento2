<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract front controller for concrete API type.
 */
abstract class Mage_Webapi_Controller_FrontAbstract implements Mage_Core_Controller_FrontInterface
{
    const BASE_ACTION_CONTROLLER = 'Mage_Webapi_Controller_ActionAbstract';

    /**#@+
     * Version limits
     */
    const VERSION_MIN = 1;
    const VERSION_MAX = 200;
    /**#@-*/

    /** @var Mage_Webapi_Controller_RequestAbstract */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Model_Config_Resource */
    protected $_resourceConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Core_Model_Config */
    protected $_applicationConfig;

    function __construct(Mage_Webapi_Helper_Data $helper = null, Mage_Core_Model_Config $applicationConfig = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
        $this->_applicationConfig = $applicationConfig ? $applicationConfig : Mage::getConfig();
    }

    /**
     * Generic action controller for all controllers in 'webapi' area
     *
     * @var string
     */
    protected $_baseActionController = self::BASE_ACTION_CONTROLLER;

    /**
     * Set response.
     *
     * @param Mage_Webapi_Controller_RequestAbstract $request
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    public function setRequest(Mage_Webapi_Controller_RequestAbstract $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve request object.
     *
     * @return Mage_Webapi_Controller_RequestAbstract
     */
    // TODO: Do we need this abstract?
    abstract public function getRequest();

    /**
     * Initialize resources config based on requested modules and versions.
     *
     * @param array $requestedModules
     */
    protected function _initResourceConfig($requestedModules)
    {
        if (is_null($this->getResourceConfig())) {
            $resourceConfigFiles = $this->_applicationConfig->getModulesApiConfigurationFiles($requestedModules);
            /** @var Mage_Webapi_Model_Config_Resource $resourceConfig */
            $resourceConfig = Mage::getModel('Mage_Webapi_Model_Config_Resource', $resourceConfigFiles);
            $this->setResourceConfig($resourceConfig);
        }
    }

    /**
     * Retrieve config describing resources available in all APIs
     * The same resource config must be used in all API types
     *
     * @return Mage_Webapi_Model_Config_Resource
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Set resource config.
     *
     * @param Mage_Webapi_Model_Config_Resource $config
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    public function setResourceConfig(Mage_Webapi_Model_Config_Resource $config)
    {
        $this->_resourceConfig = $config;
        return $this;
    }

    /**
     * Check permissions on specific resource in ACL.
     *
     * @param string $role
     * @param string $resource
     * @param string $method
     * @throws Mage_Webapi_Exception
     */
    protected function _checkResourceAcl($role, $resource, $method)
    {
        try {
            $isAllowed = Mage::getModel('Mage_Webapi_Model_Authorization')->isAllowed($role, $resource, $method);
            if (!$isAllowed) {
                throw new Mage_Webapi_Exception(
                    $this->_helper->__('Access to resource forbidden.'),
                    Mage_Webapi_Exception::HTTP_FORBIDDEN
                );
            }
        } catch (Zend_Acl_Exception $e) {
            throw new Mage_Webapi_Exception(
                $this->_helper->__('Resource not found.'),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * Set response object.
     *
     * @param Mage_Webapi_Controller_Response $response
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    public function setResponse(Mage_Webapi_Controller_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Retrieve response object.
     *
     * @return Mage_Webapi_Controller_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Add exception to response.
     *
     * @param Exception $exception
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    protected function _addException(Exception $exception)
    {
        $this->getResponse()->setException($exception);
        return $this;
    }

    /**
     * Instantiate and validate action controller
     *
     * @param string $className
     * @return Mage_Webapi_Controller_ActionAbstract
     * @throws LogicException
     */
    protected function _getActionControllerInstance($className)
    {
        Magento_Autoload::getInstance()->addFilesMap(array(
            $className => $this->_getControllerFileName($className)
        ));
        $controllerInstance = new $className($this->getRequest(), $this->getResponse());
        if (!($controllerInstance instanceof $this->_baseActionController)) {
            throw new LogicException($this->getHelper()->__('Action controller type is invalid.'));
        }

        return $controllerInstance;
    }

    /**
     * Identify controller file name by its class name
     *
     * @param string $controllerClassName
     * @return string
     * @throws LogicException
     */
    protected function _getControllerFileName($controllerClassName)
    {
        $parts = explode('_', $controllerClassName);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = $this->_applicationConfig->getModuleDir('controllers', $realModule) . DS . implode(DS, $parts) . '.php';
        if (!file_exists($file)) {
            throw new LogicException(
                $this->getHelper()->__('Action controller "%s" could not be loaded.', $controllerClassName));
        }

        return str_replace($this->_applicationConfig->getOptions()->getBaseDir(), '', $file);
    }

    /**
     * Find the most appropriate version suffix for the requested action.
     *
     * If there is no action with requested version, fallback mechanism is used.
     * If there is no appropriate action found after fallback - exception is thrown.
     *
     * @param string $operationName
     * @param Mage_Webapi_Controller_ActionAbstract $controllerInstance
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getVersionSuffix($operationName, $controllerInstance)
    {
        $originalVersion = $this->_getOperationVersion($operationName);
        $methodName = $this->getResourceConfig()->getMethodNameByOperation($operationName);
        $methodVersion = $originalVersion;
        while ($methodVersion >= self::VERSION_MIN) {
            $versionSuffix = 'V' . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $versionSuffix)) {
                return $versionSuffix;
            }
            $methodVersion--;
        }
        throw new Mage_Webapi_Exception(
            $this->getHelper()->__('The "%s" method is not implemented in version %s', $methodName, $originalVersion),
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * Identify version of requested operation
     *
     * @param string $operationName
     * @return int
     * @throws Mage_Webapi_Exception
     */
    protected function _getOperationVersion($operationName)
    {
        $requestedModules = $this->getRequest()->getRequestedModules();
        $moduleName = $this->getResourceConfig()->getModuleNameByOperation($operationName);
        if (!isset($requestedModules[$moduleName])) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('The version of "%s" operation cannot be identified.', $operationName),
                Mage_Webapi_Exception::HTTP_NOT_FOUND
            );
        }
        $version = (int)str_replace('V', '', ucfirst($requestedModules[$moduleName]));
        if ($version > self::VERSION_MAX) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__("Resource version cannot be greater than %s.", self::VERSION_MAX),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        return $version;
    }

    /**
     * Check if operation is deprecated or removed. Throw exception when necessary.
     *
     * @param string $operationName
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    protected function _checkOperationDeprecation($operationName)
    {
        if ($deprecationPolicy = $this->getResourceConfig()->getOperationDeprecationPolicy($operationName)) {
            $operationToBeUsed = isset($deprecationPolicy['use_operation'])
                ? $deprecationPolicy['use_operation']
                : $operationName;
            if (!isset($deprecationPolicy['use_version']) || empty($deprecationPolicy['use_version'])) {
                throw new LogicException($this->getHelper()
                    ->__('The "%s" operation was marked as deprecated but "use_version" attribute was not specified.',
                    $operationName));
            } else {
                $versionToBeUsed = $deprecationPolicy['use_version'];
            }
            if (isset($deprecationPolicy['removed'])) {
                throw new Mage_Webapi_Exception($this->getHelper()
                    ->__('The requested version of "%s" operation was removed. Please, use version %s of "%s" operation instead.',
                    $operationName, $versionToBeUsed, $operationToBeUsed), Mage_Webapi_Exception::HTTP_NOT_FOUND);
            } else if (isset($deprecationPolicy['deprecated']) && Mage::getIsDeveloperMode()) {
                throw new Mage_Webapi_Exception($this->getHelper()
                    ->__('The requested version of "%s" operation is deprecated. Please, use version %s of "%s" operation instead.',
                    $operationName, $versionToBeUsed, $operationToBeUsed), Mage_Webapi_Exception::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * Retrieve Webapi data helper.
     *
     * @return Mage_Webapi_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}
