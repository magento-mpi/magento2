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
    const EXCEPTION_CODE_RESOURCE_NOT_FOUND = 404;
    const EXCEPTION_CODE_RESOURCE_NOT_IMPLEMENTED = 405;

    /**#@+
     * Version limits
     */
    const VERSION_MIN = 1;
    const VERSION_MAX = 200;
    /**#@-*/

    /** @var Mage_Webapi_Model_Request */
    protected $_request;

    /** @var Mage_Webapi_Model_Response */
    protected $_response;

    /** @var Mage_Webapi_Model_Config_Resource */
    protected $_resourceConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    function __construct(Mage_Webapi_Helper_Data $helper = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
    }

    /**
     * Generic action controller for all controllers in current area
     *
     * @var string
     */
    // TODO: Initialize base action controller with value from config (currently there is single action controller for all API types)
    protected $_baseActionController = '';

    abstract public function init();

    abstract public function dispatch();

    /**
     * Initialize resources config based on requested modules and versions.
     *
     * @param array $requestedModules
     */
    protected function _initResourceConfig($requestedModules)
    {
        if (is_null($this->getResourceConfig())) {
            $resourceConfigFiles = Mage::app()->getConfig()->getModulesApiConfigurationFiles($requestedModules);
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
     * Check permissions on specific resource in ACL. No information about roles must be used on this level.
     * ACL check must be performed in the same way for all API types
     */
    protected function _checkResourceAcl()
    {
        // TODO: Implement
        return $this;
    }

    /**
     * Set request object.
     *
     * @param Mage_Webapi_Model_Request $request
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    public function setRequest(Mage_Webapi_Model_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve request object.
     *
     * @return Mage_Webapi_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object.
     *
     * @param Mage_Webapi_Model_Response $response
     * @return Mage_Webapi_Controller_FrontAbstract
     */
    public function setResponse(Mage_Webapi_Model_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Retrieve response object.
     *
     * @return Mage_Webapi_Model_Response
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
        $response = $this->getResponse();
        $response->setException($exception);
        return $this;
    }

    /**
     * Instantiate and validate action controller
     *
     * @param string $className
     * @return Mage_Webapi_Controller_ActionAbstract
     * @throws Mage_Core_Exception
     */
    protected function _getActionControllerInstance($className)
    {
        Magento_Autoload::getInstance()->addFilesMap(array(
            $className => $this->_getControllerFileName($className)
        ));
        $controllerInstance = new $className($this->getRequest(), $this->getResponse());
        if (!($controllerInstance instanceof $this->_baseActionController)) {
            throw Mage::exception('Mage_Core',
                Mage::helper('Mage_Core_Helper_Data')->__('Action controller type is invalid.'));
        }

        return $controllerInstance;
    }

    /**
     * Identify controller file name by its class name
     *
     * @param string $controllerClassName
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _getControllerFileName($controllerClassName)
    {
        $parts = explode('_', $controllerClassName);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = Mage::getModuleDir('controllers', $realModule) . DS . implode(DS, $parts) . '.php';
        if (!file_exists($file)) {
            throw Mage::exception('Mage_Core',
                Mage::helper('Mage_Core_Helper_Data')->__('Action controller "%s" could not be loaded.', $controllerClassName));
        }

        return str_replace(Mage::getBaseDir(), '', $file);
    }

    /**
     * Find the most appropriate version suffix for the requested action.
     *
     * If there is no action with requested version, fallback mechanism is used.
     * If there is no appropriate action found after fallback - exception is thrown.
     *
     * @param string $methodName
     * @param Mage_Webapi_Controller_ActionAbstract $controllerInstance
     * @return string
     * @throws RuntimeException
     */
    protected function _getAvailableMethodSuffix($methodName, $controllerInstance)
    {
        $originalVersion = $this->_getVersion();
        $methodVersion = $originalVersion;
        while ($methodVersion >= self::VERSION_MIN) {
            $methodSuffix = 'V' . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $methodSuffix)) {
                return $methodSuffix;
            }
            $methodVersion--;
        }
        throw new RuntimeException(
            $this->_helper->__('The "%s" method is not implemented in version %s', $methodName, $originalVersion),
            self::EXCEPTION_CODE_RESOURCE_NOT_IMPLEMENTED);
    }

    /**
     * Get correct version of the resource model
     *
     * @return int
     * @throws RuntimeException
     */
    protected function _getVersion()
    {
//        /** @var Mage_Webapi_Model_Request $request */
//        $request = $this->getRequest();
//        $requestedVersion = $request->getVersion();
//        if (false !== $requestedVersion && !preg_match('/^[1-9]\d*$/', $requestedVersion)) {
//            throw new Mage_Webapi_Exception(
//                sprintf('Invalid version "%s" requested.', htmlspecialchars($requestedVersion)),
//                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST
//            );
//        }
        // TODO: Implement versioning
        $version = 1;
        if ($version > self::VERSION_MAX) {
            throw new HttpInvalidParamException($this->_helper
                ->__("Resource version cannot be greater than %s.", self::VERSION_MAX));
        }
        return (int)$version;
    }

    /**
     * Retrieve reflection helper.
     *
     * @return Mage_Webapi_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}
