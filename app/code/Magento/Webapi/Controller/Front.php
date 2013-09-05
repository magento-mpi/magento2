<?php
/**
 * Front controller associated with API area.
 *
 * The main responsibility of this class is to identify requested API type and instantiate correct dispatcher for it.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Front implements Magento_Core_Controller_FrontInterface
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
     * @var Magento_Webapi_Controller_DispatcherInterface
     */
    protected $_dispatcher;

    /** @var Magento_Core_Model_App */
    protected $_application;

    /** @var string */
    protected $_apiType;

    /** @var Magento_Webapi_Controller_Dispatcher_Factory */
    protected $_dispatcherFactory;

    /** @var \Magento\Controller\Router\Route\Factory */
    protected $_routeFactory;

    /** @var Magento_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Dispatcher_Factory $dispatcherFactory
     * @param Magento_Core_Model_App $application
     * @param \Magento\Controller\Router\Route\Factory $routeFactory
     * @param Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     */
    public function __construct(
        Magento_Webapi_Controller_Dispatcher_Factory $dispatcherFactory,
        Magento_Core_Model_App $application,
        \Magento\Controller\Router\Route\Factory $routeFactory,
        Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
    ) {
        $this->_dispatcherFactory = $dispatcherFactory;
        $this->_application = $application;
        $this->_routeFactory = $routeFactory;
        $this->_errorProcessor = $errorProcessor;
    }

    /**
     * Prepare environment, initialize dispatcher.
     *
     * @return Magento_Webapi_Controller_Front
     */
    public function init()
    {
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);

        return $this;
    }

    /**
     * Dispatch request and send response.
     *
     * @return Magento_Webapi_Controller_Front
     */
    public function dispatch()
    {
        try {
            $this->_getDispatcher()->dispatch();
        } catch (Exception $e) {
            $this->_errorProcessor->renderException($e);
        }
        return $this;
    }

    /**
     * Retrieve front controller for concrete API type (factory method).
     *
     * @return Magento_Webapi_Controller_DispatcherInterface
     * @throws Magento_Core_Exception
     */
    protected function _getDispatcher()
    {
        if (is_null($this->_dispatcher)) {
            $this->_dispatcher = $this->_dispatcherFactory->get($this->determineApiType());
        }
        return $this->_dispatcher;
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
     * @throws Magento_Core_Exception
     * @throws Magento_Webapi_Exception If requested API type is invalid.
     */
    public function determineApiType()
    {
        if (is_null($this->_apiType)) {
            $request = $this->_application->getRequest();
            $apiRoutePath = $this->_application->getConfig()->getAreaFrontName()
                . '/:' . Magento_Webapi_Controller_Request::PARAM_API_TYPE;
            $apiRoute = $this->_routeFactory->createRoute(
                'Magento_Webapi_Controller_Router_Route',
                $apiRoutePath
            );
            if (!($apiTypeMatch = $apiRoute->match($request, true))) {
                throw new Magento_Webapi_Exception(__('Request does not match any API type route.'),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST);
            }

            $apiType = $apiTypeMatch[Magento_Webapi_Controller_Request::PARAM_API_TYPE];
            if (!in_array($apiType, $this->getListOfAvailableApiTypes())) {
                throw new Magento_Webapi_Exception(__('The "%1" API type is not defined.', $apiType),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST);
            }
            $this->_apiType = $apiType;
        }
        return $this->_apiType;
    }
}
