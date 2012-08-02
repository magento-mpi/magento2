<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract front controller for concrete API type.
 */
abstract class Mage_Api2_Controller_FrontAbstract implements Mage_Core_Controller_FrontInterface
{
    /** @var Mage_Api2_Model_Request */
    protected $_request;

    /** @var Mage_Api2_Model_Response */
    protected $_response;

    /** @var Mage_Api2_Model_Config_Resource */
    protected $_resourceConfig;

    /**
     * Generic action controller for all controllers in current area
     *
     * @var string
     */
    protected $_baseActionController;

    abstract public function init();

    abstract public function dispatch();

    /**
     * Retrieve config describing resources available in all APIs
     * The same resource config must be used in all API types
     *
     * @return Mage_Api2_Model_Config_Resource
     */
    public function getResourceConfig()
    {
        return $this->_resourceConfig;
    }

    /**
     * Set resource config.
     *
     * @param Mage_Api2_Model_Config_Resource $config
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setResourceConfig(Mage_Api2_Model_Config_Resource $config)
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
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setRequest(Mage_Api2_Model_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve request object.
     *
     * @return Mage_Api2_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set response object.
     *
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Controller_FrontAbstract
     */
    public function setResponse(Mage_Api2_Model_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Retrieve response object.
     *
     * @return Mage_Api2_Model_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Add exception to response.
     *
     * @param Exception $exception
     * @return Mage_Api2_Controller_FrontAbstract
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
     * @return Mage_Core_Controller_Varien_Action
     * @throws Mage_Core_Exception
     */
    protected function _getActionControllerInstance($className)
    {
        if (!$this->_validateControllerClassName($className)) {
            throw Mage::exception('Mage_Core',
                Mage::helper('Mage_Core_Helper_Data')->__('Specified action controller is not found.'));
        }

        $controllerInstance = new $className($this->getRequest(), $this->getResponse());
        if (!($controllerInstance instanceof $this->_baseActionController)) {
            throw Mage::exception('Mage_Core',
                Mage::helper('Mage_Core_Helper_Data')->__('Action controller type is invalid.'));
        }

        return $controllerInstance;
    }

    /**
     * Generating and validating class file name and include it if everything is OK.
     *
     * @param string $controllerClassName
     * @return bool
     */
    protected function _validateControllerClassName($controllerClassName)
    {
        $controllerFileName = $this->_getControllerFileName($controllerClassName);
        if (!$this->_validateControllerFileName($controllerFileName)) {
            return false;
        }

        // include controller file if needed
        if (!$this->_includeControllerClass($controllerFileName, $controllerClassName)) {
            return false;
        }
        return true;
    }

    /**
     * Include the file containing controller class if this class is not defined yet
     *
     * @param string $controllerFileName
     * @param string $controllerClassName
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _includeControllerClass($controllerFileName, $controllerClassName)
    {
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;

            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core',
                    Mage::helper('Mage_Core_Helper_Data')->__('Controller file was loaded but class does not exist'));
            }
        }
        return true;
    }

    /**
     * Check if controller file name is valid
     *
     * @param string $fileName
     * @return bool
     */
    protected function _validateControllerFileName($fileName)
    {
        if ($fileName && is_readable($fileName) && false === strpos($fileName, '//')) {
            return true;
        }
        return false;
    }

    /**
     * Identify controller file name by its class name
     *
     * @param string $controllerClassName
     * @return string
     */
    protected function _getControllerFileName($controllerClassName)
    {
        $parts = explode('_', $controllerClassName);
        $realModule = implode('_', array_splice($parts, 0, 2));
        $file = Mage::getModuleDir('controllers', $realModule) . DS . implode(DS, $parts) . '.php';
        return $file;
    }
}
