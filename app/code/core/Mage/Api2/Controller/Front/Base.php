<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Generic front controller for all API types
 */
class Mage_Api2_Controller_Front_Base implements Mage_Core_Controller_FrontInterface
{
    /**
     * List of available concrete API front controllers
     *
     * @var array array({api type} => {API front controller class})
     */
    private $_concreteFrontControllers = array(
        'rest' => 'Mage_Api2_Controller_Front_Rest',
    );

    /**
     * Specific front controller for current API type.
     * This variable must not be available in concrete front controllers
     *
     * @var Mage_Api2_Controller_Front_Base
     */
    private $_concreteFrontController;

    /**
     * Determine concrete API front controller to use. Initialize it
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function init()
    {
        try {
            // TODO: Temporary workaround. Required ability to configure request and response classes per area
            Mage::app()->setRequest(Mage::getSingleton('Mage_Api2_Model_Request'));
            Mage::app()->setResponse(Mage::getSingleton('Mage_Api2_Model_Response'));

            // TODO: Make sure that non-admin users cannot access this area
            Mage::register('isSecureArea', true, true);
            // make sure all errors will not be displayed
            ini_set('display_startup_errors', 0);
            ini_set('display_errors', 0);

            $this->_getConcreteFrontController()->_init();
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_addException($e);
        }
    }

    /**
     * Dispatch request and send response
     *
     * @return Mage_Api2_Controller_Front_Base
     */
    public function dispatch()
    {
        // TODO: Think how to implement resource ACL check here. Try to implement this method as a 'template method'
        $this->_getConcreteFrontController()->_dispatch();
    }

    /**
     * Should be implemented in child classes
     */
    protected function _init()
    {
        throw new LogicException('This method must be overridden in child');
    }

    /**
     * Should be implemented in child classes
     */
    protected function _dispatch()
    {
        throw new LogicException('This method must be overridden in child');
    }

    /**
     * Retrieve config describing resources available in all APIs
     * The same resource config must be used in all API types
     */
    final protected function _getResourceConfig()
    {
        // TODO: Implement
    }

    /**
     * Check permissions on specific resource in ACL. No information about roles must be used on this level.
     * ACL check must be performed in the same way for all API types
     */
    final protected function _checkResourceAcl()
    {
        // TODO: Implement
    }

    /**
     * Retrieve request object
     *
     * TODO: Check return type
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

    /**
     * Retrieve response object
     *
     * TODO: Check return type
     * @return Zend_Controller_Response_Http
     */
    protected function _getResponse()
    {
        return Mage::app()->getResponse();
    }

    /**
     * Set front controller for concrete API type.
     * This method must not be available in concrete front controller
     *
     * @param Mage_Api2_Controller_Front_Base $concreteFrontController
     */
    private function _setConcreteFrontController(Mage_Api2_Controller_Front_Base $concreteFrontController)
    {
        $this->_concreteFrontController = $concreteFrontController;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     * This method must not be available in concrete front controller
     *
     * @return Mage_Api2_Controller_Front_Base
     * @throws Mage_Core_Exception
     */
    private function _getConcreteFrontController()
    {
        if (is_null($this->_concreteFrontController)) {
            /** @var Mage_Api2_Model_Router $router */
            $router = Mage::getModel('Mage_Api2_Model_Router');
            // TODO: Multicall problem: currently it is not possible to pass custom request object to API type routing
            $router->routeApiType($this->_getRequest(), true);
            $apiType = $this->_getRequest()->getParam('api_type');
            if (!isset($this->_concreteFrontControllers[$apiType])) {
                throw new Mage_Core_Exception(Mage::helper('Mage_Api2_Helper_Data')
                    ->__('The specified API type "%s" is not implemented.', $apiType));
            }
            $concreteFrontControllerClass = $this->_concreteFrontControllers[$apiType];
            $this->_setConcreteFrontController(new $concreteFrontControllerClass());
        }
        return $this->_concreteFrontController;
    }

    /**
     * Add exception to response
     *
     * @param Exception $exception
     * @return Mage_Api2_Controller_Front_Base
     */
    protected function _addException(Exception $exception)
    {
        $response = $this->_getResponse();
        $response->setException($exception);
        return $this;
    }
}
