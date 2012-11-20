<?php
/**
 * Handler for REST API calls.
 *
 * @method Mage_Webapi_Controller_Request_Rest getRequest() getRequest()
 * @copyright {}
 */
class Mage_Webapi_Controller_Handler_Rest extends Mage_Webapi_Controller_HandlerAbstract
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

    const DEFAULT_SHUTDOWN_FUNCTION = 'apiShutdownFunction';

    /**
     * @var Mage_Webapi_Controller_Response_Rest_RendererInterface
     */
    protected $_renderer;

    /** @var Mage_Webapi_Controller_Handler_Rest_Presentation */
    protected $_restPresentation;

    /** @var Mage_Webapi_Controller_Handler_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Factory */
    protected $_rendererFactory;

    /** @var Mage_Core_Model_Event_Manager */
    protected $_eventManager;

    /** @var Mage_Webapi_Controller_Router_Rest */
    protected $_router;

    /** @var Mage_Webapi_Model_Rest_Oauth_Server */
    protected $_oauthServer;

    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Config $applicationConfig,
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Controller_Action_Factory $controllerFactory,
        Mage_Core_Model_Logger $logger,
        Mage_Webapi_Controller_Handler_Rest_Presentation $restPresentation,
        Mage_Webapi_Controller_Handler_ErrorProcessor $errorProcessor,
        Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Webapi_Controller_Router_Rest $router,
        Magento_ObjectManager $objectManager,
        Mage_Webapi_Model_Authorization_RoleLocator $roleLocator,
        Mage_Webapi_Model_Rest_Oauth_Server $oauthServer
    ) {
        parent::__construct(
            $helperFactory,
            $applicationConfig,
            $apiConfig,
            $requestFactory,
            $response,
            $controllerFactory,
            $logger,
            $objectManager,
            $roleLocator
        );
        $this->_restPresentation = $restPresentation;
        $this->_errorProcessor = $errorProcessor;
        $this->_rendererFactory = $rendererFactory;
        $this->_eventManager = $eventManager;
        $this->_router = $router;
        $this->_oauthServer = $oauthServer;
    }

    /**
     * Server errors processing mechanism initialization.
     *
     * @return Mage_Webapi_Controller_Handler_Rest|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        parent::init();
        // redeclare custom shutdown function to handle fatal errors correctly
        $this->registerShutdownFunction(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        return $this;
    }

    /**
     * Handle REST request.
     *
     * @return Mage_Webapi_Controller_Handler_Rest
     */
    public function handle()
    {
        try {
            $this->_authenticate();
            $route = $this->_matchRoute($this->getRequest());

            $operation = $this->_getOperationName();
            $resourceVersion = $this->_getResourceVersion($operation);
            $method = $this->getApiConfig()->getMethodNameByOperation($operation, $resourceVersion);
            $controllerClassName = $this->getApiConfig()->getControllerClassByOperationName($operation);
            $controllerInstance = $this->_getActionControllerInstance($controllerClassName);
            $versionAfterFallback = $this->_identifyVersionSuffix($operation, $resourceVersion, $controllerInstance);
            /**
             * Route check has two stages:
             * The first is performed against full list of routes that is merged from all resources.
             * The second stage of route check can be performed only when actual version to be executed is known.
             */
            $this->_checkRoute($method, $versionAfterFallback);
            $this->_checkDeprecationPolicy($route->getResourceName(), $method, $versionAfterFallback);
            $action = $method . $versionAfterFallback;

            $this->_checkResourceAcl($route->getResourceName(), $method);

            $inputData = $this->_restPresentation->fetchRequestData($controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_restPresentation->prepareResponse($method, $outputData);
        } catch (Mage_Webapi_Exception $e) {
            $this->_addException($e);
        } catch (Exception $e) {
            if (!Mage::getIsDeveloperMode()) {
                $this->_logger->logException($e);
                $this->_addException(
                    new Mage_Webapi_Exception(
                        $this->_helper->__("Internal Error. Details are available in Magento log file."),
                        Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
                    )
                );
            } else {
                $this->_addException($e);
            }
        }
        $this->_sendResponse();
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
        $resourceName = $this->getRequest()->getResourceName();
        $routes = $this->getApiConfig()->getMethodRestRoutes($resourceName, $methodName, $version);
        foreach ($routes as $route) {
            if ($route->match($this->getRequest())) {
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
        $this->getRequest()->setResourceName($route->getResourceName());
        $this->getRequest()->setResourceType($route->getResourceType());
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
        $httpMethod = $this->getRequest()->getHttpMethod();
        $resourceType = $this->getRequest()->getResourceType();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            throw new Mage_Webapi_Exception($this->_helper->__('Requested method does not exist.'),
                Mage_Webapi_Exception::HTTP_NOT_FOUND);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        if ($methodName == self::HTTP_METHOD_CREATE) {
            /** If request is numeric array, multi create operation must be used. */
            $params = $this->getRequest()->getBodyParams();
            if (count($params)) {
                $keys = array_keys($params);
                if (is_numeric($keys[0])) {
                    $methodName = Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE;
                }
            }
        }
        $operationName = $this->getRequest()->getResourceName() . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Authenticate user.
     *
     * @throws Mage_Webapi_Exception
     */
    protected function _authenticate()
    {
        try {
            $consumer = $this->_oauthServer->authenticateTwoLegged();
            $this->_roleLocator->setRoleId($consumer->getRoleId());
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($this->_oauthServer->reportProblem($e),
                Mage_Webapi_Exception::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Redeclare custom shutdown function.
     *
     * @param   string $handler
     * @return  Mage_Webapi_Controller_Handler_Rest
     */
    public function registerShutdownFunction($handler)
    {
        register_shutdown_function($handler);
        return $this;
    }

    /**
     * Send response to the client, render exceptions if they are present.
     */
    protected function _sendResponse()
    {
        try {
            if ($this->getResponse()->isException()) {
                $this->_renderMessages();
            }
            $this->getResponse()->sendResponse();
        } catch (Exception $e) {
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            $httpCode = $e->getCode() == Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                ? Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;

            /** If error was encountered during "error rendering" process then use error renderer. */
            $this->_errorProcessor->renderException($e, $httpCode);
        }
    }

    /**
     * Generate and set HTTP response code, error messages to Response object.
     */
    protected function _renderMessages()
    {
        $response = $this->getResponse();
        $formattedMessages = array();
        $formattedMessages['messages'] = $response->getMessages();
        $responseHttpCode = null;
        /** @var Exception $exception */
        foreach ($response->getException() as $exception) {
            $code = ($exception instanceof Mage_Webapi_Exception)
                ? $exception->getCode()
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;
            $messageData = array('code' => $code, 'message' => $exception->getMessage());
            if (Mage::getIsDeveloperMode()) {
                $messageData['trace'] = $exception->getTraceAsString();
            }
            $formattedMessages['messages']['error'][] = $messageData;
            // keep HTTP code for response
            $responseHttpCode = $code;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $response->setHttpResponseCode($responseHttpCode);
        $response->setMimeType($this->_getRenderer()->getMimeType());
        $response->setBody($this->_getRenderer()->render($formattedMessages));
        return $this;
    }

    /**
     * Get renderer object according to request accepted mime type.
     *
     * @return Mage_Webapi_Controller_Response_Rest_RendererInterface
     */
    protected function _getRenderer()
    {
        if (!$this->_renderer) {
            $this->_renderer = $this->_rendererFactory->create($this->getRequest()->getAcceptTypes());
        }
        return $this->_renderer;
    }

    /**
     * Identify version of resource associated with requested operation.
     *
     * @return int
     * @throws LogicException
     */
    protected function _getResourceVersion()
    {
        $resourceVersion = $this->getRequest()->getResourceVersion();
        if (is_null($resourceVersion)) {
            throw new LogicException(
                "Please be sure to call Mage_Webapi_Controller_Request_Rest::setResourceVersion() first.");
        }
        $this->_validateVersionNumber($resourceVersion, $this->getRequest()->getResourceName());
        return $resourceVersion;
    }

    /**
     * Function to catch errors, that has not been caught by the user error handler function.
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function apiShutdownFunction()
    {
        $fatalErrorFlag = E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR;
        $error = error_get_last();
        if ($error && ($error['type'] & $fatalErrorFlag)) {
            $errorMessage = '';
            switch ($error['type']) {
                case E_ERROR:
                    $errorMessage .= "Fatal Error";
                    break;
                case E_PARSE:
                    $errorMessage .= "Parse Error";
                    break;
                case E_CORE_ERROR:
                    $errorMessage .= "Core Error";
                    break;
                case E_COMPILE_ERROR:
                    $errorMessage .= "Compile Error";
                    break;
                case E_USER_ERROR:
                    $errorMessage .= "User Error";
                    break;
                case E_RECOVERABLE_ERROR:
                    $errorMessage .= "Recoverable Error";
                    break;
                default:
                    $errorMessage .= "Unknown error ({$error['type']})";
                    break;
            }
            $errorMessage .= ": {$error['message']}  in {$error['file']} on line {$error['line']}";
            try {
                // call registered error handler
                trigger_error("'$errorMessage'", E_USER_ERROR);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
            if (!Mage::getIsDeveloperMode()) {
                $this->_errorProcessor->saveReport($errorMessage);
            }
            $this->_errorProcessor->render($errorMessage);
        }
    }
}
