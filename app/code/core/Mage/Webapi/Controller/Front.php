<?php
/**
 * Front controller associated with API area.
 *
 * The main responsibility of this class is to identify requested API type and instantiate correct handler for it.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Front implements Mage_Core_Controller_FrontInterface
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
        self::API_TYPE_REST => 'Mage_Webapi_Controller_Handler_Rest',
        self::API_TYPE_SOAP => 'Mage_Webapi_Controller_Handler_Soap',
    );

    /**
     * Specific front controller for current API type.
     *
     * @var Mage_Webapi_Controller_HandlerAbstract
     */
    protected $_concreteFrontController;

    /** @var Mage_Webapi_Controller_Request */
    protected $_apiRequest;

    /** @var Mage_Webapi_Controller_Response */
    protected $_apiResponse;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var string */
    protected $_apiType;

    /** @var Mage_Webapi_Controller_HandlerFactory */
    protected $_frontControllerFactory;

    /** @var Mage_Webapi_Controller_RequestFactory */
    protected $_requestFactory;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Webapi_Controller_Handler_ErrorProcessor */
    protected $_errorProcessor;

    function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Webapi_Controller_HandlerFactory $frontControllerFactory,
        Mage_Webapi_Controller_RequestFactory $requestFactory,
        Mage_Webapi_Controller_Response $response,
        Mage_Core_Model_App $application,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Webapi_Controller_Handler_ErrorProcessor $errorProcessor
    ) {
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_frontControllerFactory = $frontControllerFactory;
        $this->_requestFactory = $requestFactory;
        $this->_apiResponse = $response;
        $this->_application = $application;
        $this->_routeFactory = $routeFactory;
        $this->_errorProcessor = $errorProcessor;
    }

    /**
     * Determine concrete API front controller to use. Initialize it
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function init()
    {
        try {
            $this->_apiRequest = $this->_requestFactory->get($this->_determineApiType());

            // TODO: Make sure that non-admin users cannot access this area
            Mage::register('isSecureArea', true, true);
            // make sure that all errors will not be displayed
            ini_set('display_startup_errors', 0);
            ini_set('display_errors', 0);

            $this->_getConcreteFrontController()->setRequest($this->_apiRequest)->init();
        } catch (Mage_Webapi_Exception $e) {
            $this->_errorProcessor->render($e->getMessage(), $e->getTraceAsString(), $e->getCode());
            die();
        }
        return $this;
    }

    /**
     * Dispatch request and send response
     *
     * @return Mage_Webapi_Controller_Front
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
     * @param Mage_Webapi_Controller_HandlerAbstract $concreteFrontController
     */
    protected function _setConcreteFrontController(Mage_Webapi_Controller_HandlerAbstract $concreteFrontController)
    {
        $this->_concreteFrontController = $concreteFrontController;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     *
     * @return Mage_Webapi_Controller_HandlerAbstract
     * @throws Mage_Core_Exception
     */
    protected function _getConcreteFrontController()
    {
        if (is_null($this->_concreteFrontController)) {
            $apiType = $this->_determineApiType();

            $concreteFrontControllerClass = $this->_concreteFrontControllers[$apiType];
            $this->_setConcreteFrontController(
                $this->_frontControllerFactory->create($concreteFrontControllerClass)
            );
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
            $request = $this->_application->getRequest();
            $apiRoute = $this->_routeFactory->createRoute(
                'Mage_Webapi_Controller_Router_Route_Webapi',
                Mage_Webapi_Controller_Router_Route_Webapi::getApiRoute()
            );
            if (!($apiTypeMatch = $apiRoute->match($request, true))) {
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
