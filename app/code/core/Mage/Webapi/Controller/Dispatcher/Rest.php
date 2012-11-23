<?php
/**
 * Dispatcher for REST API calls.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Rest extends Mage_Webapi_Controller_DispatcherAbstract
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /**#@+
     * Resource types.
     */
    const ACTION_TYPE_ITEM = 'item';
    const ACTION_TYPE_COLLECTION = 'collection';
    /**#@-*/

    /**#@+
     * HTTP methods supported by REST.
     */
    const HTTP_METHOD_CREATE = 'create';
    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_UPDATE = 'update';
    const HTTP_METHOD_DELETE = 'delete';
    /**#@-*/

    /**#@+
     *  Default error messages.
     */
    const RESOURCE_FORBIDDEN = 'Access to resource is forbidden.';
    const RESOURCE_NOT_FOUND = 'Resource is not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method is not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data is invalid.';
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    /**#@-*/

    /**#@+
     *  Default collection resources error messages.
     */
    const RESOURCE_COLLECTION_PAGING_ERROR = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_PAGING_LIMIT_ERROR = 'The paging limit exceeds the allowed number.';
    const RESOURCE_COLLECTION_ORDERING_ERROR = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**#@+
     *  Default success messages.
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource is updated successfully.';
    /**#@-*/

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
     * @var Mage_Webapi_Controller_Action_Factory
     */
    protected $_controllerFactory;

    /** @var Mage_Webapi_Model_Authorization */
    protected $_authorization;

    /** @var Mage_Core_Model_Logger */
    protected $_logger;

    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config $apiConfig
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @param Mage_Webapi_Controller_Response_Rest $response
     * @param Mage_Webapi_Controller_Action_Factory $controllerFactory
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation
     * @param Mage_Webapi_Controller_Router_Rest $router
     * @param Mage_Webapi_Model_Authorization $authorization
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Request_Rest $request,
        Mage_Webapi_Controller_Response_Rest $response,
        Mage_Webapi_Controller_Action_Factory $controllerFactory,
        Mage_Core_Model_Logger $logger,
        Mage_Webapi_Controller_Dispatcher_Rest_Presentation $restPresentation,
        Mage_Webapi_Controller_Router_Rest $router,
        Mage_Webapi_Model_Authorization $authorization,
        Mage_Webapi_Controller_Dispatcher_Rest_Authentication $authentication
    ) {
        parent::__construct(
            $helper,
            $apiConfig
        );
        $this->_restPresentation = $restPresentation;
        $this->_router = $router;
        $this->_authentication = $authentication;
        $this->_request = $request;
        $this->_controllerFactory = $controllerFactory;
        $this->_authorization = $authorization;
        $this->_logger = $logger;
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
            $route = $this->_matchRoute($this->_request);

            $operation = $this->_getOperationName();
            $resourceVersion = $this->_getResourceVersion($operation);
            $method = $this->getApiConfig()->getMethodNameByOperation($operation, $resourceVersion);
            $controllerClassName = $this->getApiConfig()->getControllerClassByOperationName($operation);
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
            $this->_checkRoute($method, $versionAfterFallback);
            $this->_apiConfig->checkDeprecationPolicy($route->getResourceName(), $method, $versionAfterFallback);
            $action = $method . $versionAfterFallback;

            $this->_authorization->checkResourceAcl($route->getResourceName(), $method);

            $inputData = $this->_restPresentation->fetchRequestData($controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_restPresentation->prepareResponse($method, $outputData);
        } catch (Mage_Webapi_Exception $e) {
            $this->_response->setException($e);
        } catch (Exception $e) {
            // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
            if (!Mage::getIsDeveloperMode()) {
                $this->_logger->logException($e);
                $this->_response->setException(
                    new Mage_Webapi_Exception(
                        $this->_helper->__("Internal Error. Details are available in Magento log file."),
                        Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
                    )
                );
            } else {
                $this->_response->setException($e);
            }
        }
        $this->_response->sendResponse();
        return $this;
    }

    /**
     * Check whether current request matches any route of specified method or not. Method version is taken into account.
     *
     * @param string $methodName
     * @param string $version
     * @throws Mage_Webapi_Exception In case when request does not match any route of specified method.
     */
    protected function _checkRoute($methodName, $version)
    {
        $resourceName = $this->_request->getResourceName();
        $routes = $this->getApiConfig()->getMethodRestRoutes($resourceName, $methodName, $version);
        foreach ($routes as $route) {
            if ($route->match($this->_request)) {
                return;
            }
        }
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }

    /**
     * Set all routes of the given API type to Route object.
     * Find route that matches current URL, set parameters of the route to Request object.
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _matchRoute(Mage_Webapi_Controller_Request_Rest $request)
    {
        $this->_router->setRoutes($this->getApiConfig()->getAllRestRoutes());
        $route = $this->_router->match($request);
        /** Initialize additional request parameters using data from route */
        $this->_request->setResourceName($route->getResourceName());
        $this->_request->setResourceType($route->getResourceType());
        return $route;
    }

    /**
     * Identify operation name according to HTTP request parameters.
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getOperationName()
    {
        $restMethodsMap = array(
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_CREATE =>
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_GET =>
                Mage_Webapi_Controller_ActionAbstract::METHOD_LIST,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_UPDATE =>
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE,
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_DELETE =>
                Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_GET => Mage_Webapi_Controller_ActionAbstract::METHOD_GET,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_UPDATE => Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE,
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_DELETE => Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
        );
        $httpMethod = $this->_request->getHttpMethod();
        $resourceType = $this->_request->getResourceType();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            throw new Mage_Webapi_Exception($this->_helper->__('Requested method does not exist.'),
                Mage_Webapi_Exception::HTTP_NOT_FOUND);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        if ($methodName == self::HTTP_METHOD_CREATE) {
            /** If request is numeric array, multi create operation must be used. */
            $params = $this->_request->getBodyParams();
            if (count($params)) {
                $keys = array_keys($params);
                if (is_numeric($keys[0])) {
                    $methodName = Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE;
                }
            }
        }
        $operationName = $this->_request->getResourceName() . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Identify version of resource associated with requested operation.
     *
     * @return int
     * @throws LogicException
     */
    protected function _getResourceVersion()
    {
        $resourceVersion = $this->_request->getResourceVersion();
        if (is_null($resourceVersion)) {
            throw new LogicException(
                "Please be sure to call Mage_Webapi_Controller_Request_Rest::setResourceVersion() first.");
        }
        $this->_apiConfig->validateVersionNumber($resourceVersion, $this->_request->getResourceName());
        return $resourceVersion;
    }
}
