<?php
/**
 * Dispatcher for REST API calls.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Rest implements  Magento_Webapi_Controller_DispatcherInterface
{
    /** @var Magento_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Presentation */
    protected $_restPresentation;

    /** @var Magento_Webapi_Controller_Router_Rest */
    protected $_router;

    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Authentication */
    protected $_authentication;

    /** @var Magento_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Action controller factory.
     *
     * @var Magento_Webapi_Controller_Action_Factory
     */
    protected $_controllerFactory;

    /** @var Magento_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Magento_Webapi_Controller_Response_Rest */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Rest $apiConfig
     * @param Magento_Webapi_Controller_Request_Rest $request
     * @param Magento_Webapi_Controller_Response_Rest $response
     * @param Magento_Webapi_Controller_Action_Factory $controllerFactory
     * @param Magento_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation
     * @param Magento_Webapi_Controller_Router_Rest $router
     * @param Magento_Webapi_Model_Authorization $authorization
     * @param Magento_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
     */
    public function __construct(
        Magento_Webapi_Model_Config_Rest $apiConfig,
        Magento_Webapi_Controller_Request_Rest $request,
        Magento_Webapi_Controller_Response_Rest $response,
        Magento_Webapi_Controller_Action_Factory $controllerFactory,
        Magento_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation,
        Magento_Webapi_Controller_Router_Rest $router,
        Magento_Webapi_Model_Authorization $authorization,
        Magento_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_restPresentation = $restPresentation;
        $this->_router = $router;
        $this->_authentication = $authentication;
        $this->_request = $request;
        $this->_controllerFactory = $controllerFactory;
        $this->_authorization = $authorization;
        $this->_response = $response;
    }

    /**
     * Handle REST request.
     *
     * @return Magento_Webapi_Controller_Dispatcher_Rest
     */
    public function dispatch()
    {
        try {
            $this->_authentication->authenticate();
            $route = $this->_router->match($this->_request);

            $operation = $this->_request->getOperationName();
            $resourceVersion = $this->_request->getResourceVersion();
            $this->_apiConfig->validateVersionNumber($resourceVersion, $this->_request->getResourceName());
            $method = $this->_apiConfig->getMethodNameByOperation($operation, $resourceVersion);
            $controllerClassName = $this->_apiConfig->getControllerClassByOperationName($operation);
            $controllerInstance = $this->_controllerFactory->createActionController(
                $controllerClassName,
                $this->_request
            );
            $versionAfterFallback = $this->_apiConfig->identifyVersionSuffix(
                $operation,
                $resourceVersion,
                $controllerInstance
            );
            /**
             * Route check has two stages:
             * The first is performed against full list of routes that is merged from all resources.
             * The second stage of route check can be performed only when actual version to be executed is known.
             */
            $this->_router->checkRoute($this->_request, $method, $versionAfterFallback);
            $this->_apiConfig->checkDeprecationPolicy($route->getResourceName(), $method, $versionAfterFallback);
            $action = $method . $versionAfterFallback;

            $this->_authorization->checkResourceAcl($route->getResourceName(), $method);

            $inputData = $this->_restPresentation->fetchRequestData($controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_restPresentation->prepareResponse($method, $outputData);
        } catch (Exception $e) {
            $this->_response->setException($e);
        }
        $this->_response->sendResponse();
        return $this;
    }
}
