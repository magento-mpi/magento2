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
     * Specific front controller for current API type.
     *
     * @var Mage_Webapi_Controller_HandlerAbstract
     */
    protected $_handler;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var string */
    protected $_apiType;

    /** @var Mage_Webapi_Controller_Handler_Factory */
    protected $_handlerFactory;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Webapi_Controller_Handler_ErrorProcessor */
    protected $_errorProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Webapi_Controller_Handler_Factory $handlerFactory
     * @param Mage_Core_Model_App $application
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     * @param Mage_Webapi_Controller_Handler_ErrorProcessor $errorProcessor
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Webapi_Controller_Handler_Factory $handlerFactory,
        Mage_Core_Model_App $application,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Webapi_Controller_Handler_ErrorProcessor $errorProcessor
    ) {
        $this->_helper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_handlerFactory = $handlerFactory;
        $this->_application = $application;
        $this->_routeFactory = $routeFactory;
        $this->_errorProcessor = $errorProcessor;
    }

    /**
     * Prepare environment, initialize handler.
     *
     * @return Mage_Core_Controller_Varien_Front
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function init()
    {
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);
        try {
            $this->_getHandler()->init();
        } catch (Exception $e) {
            $this->_errorProcessor->renderException($e);
            /** Request processing must be stopped at this point to prevent output in unacceptable format. */
            die();
        }
        return $this;
    }

    /**
     * Dispatch request and send response.
     *
     * @return Mage_Webapi_Controller_Front
     */
    public function dispatch()
    {
        $this->_getHandler()->handle();
        return $this;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     *
     * @return Mage_Webapi_Controller_HandlerAbstract
     * @throws Mage_Core_Exception
     */
    protected function _getHandler()
    {
        if (is_null($this->_handler)) {
            $this->_handler = $this->_handlerFactory->get($this->determineApiType());
        }
        return $this->_handler;
    }

    /**
     * Return the list of defined API types.
     *
     * @return array
     */
    public function getListOfAvailableApiTypes()
    {
        return array(
            self::API_TYPE_REST,
            self::API_TYPE_SOAP
        );
    }

    /**
     * Determine current API type using application request (not web API request).
     *
     * @return string
     * @throws Mage_Core_Exception
     * @throws Mage_Webapi_Exception If requested API type is invalid.
     */
    public function determineApiType()
    {
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
            if (!in_array($apiType, $this->getListOfAvailableApiTypes())) {
                throw new Mage_Webapi_Exception($this->_helper->__('The "%s" API type is not defined.', $apiType),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST);
            }
            $this->_apiType = $apiType;
        }
        return $this->_apiType;
    }
}
