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
// TODO: Add profiler calls
class Mage_Webapi_Controller_Front_Base implements Mage_Core_Controller_FrontInterface
{
    /**
     * List of available concrete API front controllers
     *
     * @var array array({api type} => {API front controller class})
     */
    protected $_concreteFrontControllers = array(
        'rest' => 'Mage_Webapi_Controller_Front_Rest',
        'soap' => 'Mage_Webapi_Controller_Front_Soap',
    );

    /**
     * Specific front controller for current API type.
     *
     * @var Mage_Webapi_Controller_FrontAbstract
     */
    protected $_concreteFrontController;

    /** @var Mage_Webapi_Model_Request */
    protected $_request;

    /** @var Mage_Webapi_Model_Response */
    protected $_response;

    /**
     * Determine concrete API front controller to use. Initialize it
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function init()
    {
        // TODO: Temporary workaround. Required ability to configure request and response classes per area
        Mage::app()->setRequest(Mage::getSingleton('Mage_Webapi_Model_Request'));
        Mage::app()->setResponse(Mage::getSingleton('Mage_Webapi_Model_Response'));

        $this->_request = Mage::getSingleton('Mage_Webapi_Model_Request');
        $this->_response = Mage::getSingleton('Mage_Webapi_Model_Response');

        // TODO: Make sure that non-admin users cannot access this area
        Mage::register('isSecureArea', true, true);
        // make sure that all errors will not be displayed
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);

        // TODO: Implement error handling on this stage
        $concreteFrontController = $this->_getConcreteFrontController();

        $resourceConfigFiles = Mage::getConfig()->getModuleConfigurationFiles('api_resource.xml');
        /** @var Mage_Webapi_Model_Config_Resource $resourceConfig */
        $resourceConfig = Mage::getModel('Mage_Webapi_Model_Config_Resource', $resourceConfigFiles);
        $concreteFrontController->setResourceConfig($resourceConfig)
            ->setRequest($this->_request)
            ->setResponse($this->_response)
            ->init();

        return $this;
    }

    /**
     * Dispatch request and send response
     *
     * @return Mage_Webapi_Controller_Front_Base
     */
    public function dispatch()
    {
        // TODO: Think how to implement resource ACL check here. Try to implement this method as a 'template method'
        $this->_getConcreteFrontController()->dispatch();
        return $this;
    }

    /**
     * Set front controller for concrete API type.
     *
     * @param Mage_Webapi_Controller_FrontAbstract $concreteFrontController
     */
    protected function _setConcreteFrontController(Mage_Webapi_Controller_FrontAbstract $concreteFrontController)
    {
        $this->_concreteFrontController = $concreteFrontController;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     *
     * @return Mage_Webapi_Controller_FrontAbstract
     * @throws Mage_Core_Exception
     */
    protected function _getConcreteFrontController()
    {
        if (is_null($this->_concreteFrontController)) {
            $apiType = $this->_determineApiType();

            if (!isset($this->_concreteFrontControllers[$apiType])) {
                throw new Mage_Core_Exception(Mage::helper('Mage_Webapi_Helper_Data')
                    ->__('The specified API type "%s" is not implemented.', $apiType));
            }
            $concreteFrontControllerClass = $this->_concreteFrontControllers[$apiType];
            $this->_setConcreteFrontController(new $concreteFrontControllerClass());
        }
        return $this->_concreteFrontController;
    }

    /**
     * Determine API type from request.
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    private function _determineApiType()
    {
        // TODO: Multicall problem: currently it is not possible to pass custom request object to API type routing
        $request = $this->_request;
        $apiTypeRoute = new Mage_Webapi_Controller_Router_Route_ApiType();

        if (!($apiTypeMatch = $apiTypeRoute->match($request, true))) {
            throw new Mage_Core_Exception(Mage::helper('Mage_Webapi_Helper_Data')
                ->__('Request does not match any API type route.'));
        }

        $apiType = $apiTypeMatch['api_type'];
        // TODO: required for multicall, needs refactoring
        $request->setParam('api_type', $apiType);

        return $apiType;
    }
}
