<?php
/**
 * Dispatcher for REST API calls.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Rest implements  Mage_Webapi_Controller_DispatcherInterface
{
    /** @var Mage_Core_Service_Config */
    protected $_serviceConfig;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Presentation */
    protected $_restPresentation;

    /** @var Mage_Webapi_Controller_Router_Rest */
    protected $_router;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Authentication */
    protected $_authentication;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Action controller factory.
     *
     * @var Mage_Core_Service_Factory
     */
    protected $_serviceFactory;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Service_Config $serviceConfig
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @param Mage_Webapi_Controller_Response_Rest $response
     * @param Mage_Core_Service_Factory $serviceFactory
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation
     * @param Mage_Webapi_Controller_Router_Rest $router
     * @param Mage_Webapi_Model_Authorization $authorization
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
     */
    public function __construct(
        Mage_Core_Service_Config $serviceConfig,
        Mage_Webapi_Controller_Request_Rest $request,
        Mage_Webapi_Controller_Response_Rest $response,
        Mage_Core_Service_Factory $serviceFactory,
        Mage_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation,
        Mage_Webapi_Controller_Router_Rest $router,
        Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
    ) {
        $this->_serviceConfig = $serviceConfig;
        $this->_restPresentation = $restPresentation;
        $this->_router = $router;
        $this->_authentication = $authentication;
        $this->_request = $request;
        $this->_serviceFactory = $serviceFactory;
        $this->_authorization = $authorization;
        $this->_response = $response;
    }

    /**
     * Handle REST request.
     *
     * @return Mage_Webapi_Controller_Dispatcher_Rest
     */
    public function dispatch()
    {
        try {
            $this->_authentication->authenticate();
            $route = $this->_router->match($this->_request);
            $serviceName = $this->_request->getServiceName();
            $serviceInstance = $this->_serviceFactory->createServiceInstance($serviceName);
            $method = $this->_request->getMethodName();
            $this->_serviceConfig->checkDeprecationPolicy($route->getServiceName(), $method);

            /*
             * TODO: Fix ACL and Enable ACL Check.
             * TODO: Uncomment check in test Mage_Webapi_Controller_Dispatcher_RestTest::testDispatch()
             */
            // $this->_authorization->checkResourceAcl($route->getServiceName(), $method);

            $inputData = $this->_restPresentation->fetchRequestData($serviceInstance, $method);

            try {
                $outputData = call_user_func_array(array($serviceInstance, $method), $inputData);
            } catch (Mage_Service_ResourceNotFoundException $e) {
            } catch (Mage_Service_Exception $e) {
            } catch (Exception $e) {
            }
            $this->_restPresentation->prepareResponse($this->_request->getHttpMethod(), $outputData);
        } catch (Exception $e) {
            $this->_response->setException($e);
        }
        $this->_response->sendResponse();
        return $this;
    }
}
