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
 * Generic front controller for all API types
 */
// TODO: Add profiler calls
class Mage_Webapi_Controller_Front_Base implements Mage_Core_Controller_FrontInterface
{
    /**#@+
     * API types
     */
    const API_TYPE_REST = 'rest';
    const API_TYPE_SOAP = 'soap';
    /**#@-*/

    /**
     * List of available concrete API front controllers
     *
     * @var array array({api type} => {API front controller class})
     */
    protected $_concreteFrontControllers = array(
        self::API_TYPE_REST => 'Mage_Webapi_Controller_Front_Rest',
        self::API_TYPE_SOAP => 'Mage_Webapi_Controller_Front_Soap',
    );

    /**
     * Specific front controller for current API type.
     *
     * @var Mage_Webapi_Controller_FrontAbstract
     */
    protected $_concreteFrontController;

    /** @var Mage_Webapi_Controller_RequestAbstract */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var string */
    protected $_apiType;

    function __construct(Mage_Webapi_Helper_Data $helper = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
    }

    /**
     * Determine concrete API front controller to use. Initialize it
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function init()
    {
        try {
            $this->_request = Mage_Webapi_Controller_RequestAbstract::createRequest($this->_determineApiType());
            $this->_response = Mage::getSingleton('Mage_Webapi_Controller_Response');

            // TODO: Make sure that non-admin users cannot access this area
            Mage::register('isSecureArea', true, true);
            // make sure that all errors will not be displayed
            ini_set('display_startup_errors', 0);
            ini_set('display_errors', 0);

            $this->_getConcreteFrontController()->setRequest($this->_request)->setResponse($this->_response)->init();
        } catch (Mage_Webapi_Exception $e) {
            /** @var $restErrorProcessor Mage_Webapi_Controller_Front_Rest_ErrorProcessor */
            $restErrorProcessor = Mage::getModel('Mage_Webapi_Controller_Front_Rest_ErrorProcessor');
            $restErrorProcessor->render($e->getMessage(), $e->getTraceAsString(), $e->getCode());
            die();
        }
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
     * @throws Mage_Webapi_Exception If requested API type is invalid.
     */
    private function _determineApiType()
    {
        // TODO: Multicall problem: currently it is not possible to pass custom request object to API type routing
        if (is_null($this->_apiType)) {
            $request = Mage::app()->getRequest();
            $apiTypeRoute = new Mage_Webapi_Controller_Router_Route_ApiType();

            if (!($apiTypeMatch = $apiTypeRoute->match($request, true))) {
                throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any API type route.'),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST);
            }

            $apiType = $apiTypeMatch['api_type'];
            if (!array_key_exists($apiType, $this->_concreteFrontControllers)) {
                throw new Mage_Webapi_Exception($this->_helper->__('The "%s" API type is not defined.', $apiType),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST);
            }
            // TODO: required for multicall, needs refactoring
            $request->setParam('api_type', $apiType);
            $this->_apiType = $apiType;
        }

        return $this->_apiType;
    }
}
