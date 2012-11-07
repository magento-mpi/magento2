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

    const VERSION_MIN = 1;

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
     * @throws Mage_Webapi_Exception
     */
    protected function _initResourceConfig()
    {
        if (is_null($this->getResourceConfig())) {
            /** @var Mage_Webapi_Model_Config_Resource $resourceConfig */
            $resourceConfig = Mage::getModel('Mage_Webapi_Model_Config_Resource');
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
     * @param string $resource
     * @param string $method
     * @throws Mage_Webapi_Exception
     */
    protected function _checkResourceAcl($resource, $method)
    {
        try {
            /** @var Mage_Core_Model_Authorization $authorization */
            $authorization = Mage::getSingleton('Mage_Core_Model_Authorization');
            if (!$authorization->isAllowed($resource . Mage_Webapi_Model_Acl_Rule::RESOURCE_SEPARATOR . $method)
                && !$authorization->isAllowed(Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID)) {
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
     * @param int $requestedVersion
     * @param Mage_Webapi_Controller_ActionAbstract $controllerInstance
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _identifyVersionSuffix($operationName, $requestedVersion, $controllerInstance)
    {
        $methodName = $this->getResourceConfig()->getMethodNameByOperation($operationName, $requestedVersion);
        $methodVersion = $requestedVersion;
        while ($methodVersion >= self::VERSION_MIN) {
            $versionSuffix = 'V' . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $versionSuffix)) {
                return $versionSuffix;
            }
            $methodVersion--;
        }
        throw new Mage_Webapi_Exception($this->getHelper()
                ->__('The "%s" operation is not implemented in version %s', $operationName, $requestedVersion),
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
    }

    /**
     * Check if specified method is deprecated or removed.
     *
     * Throw exception in two cases:<br/>
     * - method is removed<br/>
     * - method is deprecated and developer mode is enabled
     *
     * @param string $resourceName
     * @param string $method
     * @param string $resourceVersion
     * @throws Mage_Webapi_Exception
     * @throws LogicException
     */
    protected function _checkDeprecationPolicy($resourceName, $method, $resourceVersion)
    {
        $deprecationPolicy = $this->getResourceConfig()->getDeprecationPolicy($resourceName, $method, $resourceVersion);
        if ($deprecationPolicy) {
            /** Initialize message with information about what method should be used instead of requested one. */
            if (isset($deprecationPolicy['use_resource']) && isset($deprecationPolicy['use_method'])
                && isset($deprecationPolicy['use_version'])
            ) {
                $messageUseMethod = $this->getHelper()
                    ->__('Please, use version "%s" of "%s" method in "%s" resource instead.',
                    $deprecationPolicy['use_version'], $deprecationPolicy['use_method'],
                    $deprecationPolicy['use_resource']);
            } else {
                $messageUseMethod = '';
            }

            $badRequestCode = Mage_Webapi_Exception::HTTP_BAD_REQUEST;
            if (isset($deprecationPolicy['removed'])) {
                $messageMethodRemoved = $this->getHelper()
                    ->__('Version "%s" of "%s" method in "%s" resource was removed.', $resourceVersion, $method,
                    $resourceName);
                throw new Mage_Webapi_Exception($messageMethodRemoved . ' ' . $messageUseMethod, $badRequestCode);
            } elseif (isset($deprecationPolicy['deprecated']) && Mage::getIsDeveloperMode()) {
                $messageMethodDeprecated = $this->getHelper()
                    ->__('Version "%s" of "%s" method in "%s" resource is deprecated.', $resourceVersion, $method,
                    $resourceName);
                throw new Mage_Webapi_Exception($messageMethodDeprecated . ' ' . $messageUseMethod, $badRequestCode);
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

    /**
     * Check if version number is from valid range.
     *
     * @param int $version
     * @param string $resourceName
     * @throws Mage_Webapi_Exception
     */
    protected function _validateVersionNumber($version, $resourceName)
    {
        $maxVersion = $this->getResourceConfig()->getResourceMaxVersion($resourceName);
        if ((int)$version > $maxVersion) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('The maximum version of the requested resource is "%s".', $maxVersion),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        } elseif ((int)$version < self::VERSION_MIN) {
            throw new Mage_Webapi_Exception(
                $this->getHelper()->__('Resource version cannot be lower than "%s".', self::VERSION_MIN),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
    }
}
