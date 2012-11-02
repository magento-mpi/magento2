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
 * Front controller for REST API
 */
// TODO: Add profiler calls
class Mage_Webapi_Controller_Front_Rest extends Mage_Webapi_Controller_FrontAbstract
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /**#@+
     * Resource types
     */
    const ACTION_TYPE_ITEM = 'item';
    const ACTION_TYPE_COLLECTION = 'collection';
    /**#@-*/

    /**#@+
     * HTTP methods supported by REST
     */
    const HTTP_METHOD_CREATE = 'create';
    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_UPDATE = 'update';
    const HTTP_METHOD_DELETE = 'delete';
    /**#@-*/

    /**#@+
     *  Default error messages
     */
    const RESOURCE_FORBIDDEN = 'Access to resource forbidden.';
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data invalid.';
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    /**#@-*/

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_PAGING_LIMIT_ERROR = 'The paging limit exceeds the allowed number.';
    const RESOURCE_COLLECTION_ORDERING_ERROR = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**#@+
     *  Default success messages
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource updated successful.';
    /**#@-*/

    const DEFAULT_SHUTDOWN_FUNCTION = 'mageApiShutdownFunction';

    /**
     * @var Mage_Webapi_Controller_Response_RendererInterface
     */
    protected $_renderer;

    /** @var Mage_Webapi_Controller_Front_Rest_Presentation */
    protected $_presentation;

    /**
     * Get REST request.
     *
     * @return Mage_Webapi_Controller_Request_Rest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Server errors processing mechanism initialization.
     *
     * @return Mage_Webapi_Controller_Front_Rest|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        // redeclare custom shutdown function to handle fatal errors correctly
        $this->registerShutdownFunction(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        $this->_presentation = Mage::getModel('Mage_Webapi_Controller_Front_Rest_Presentation', $this);
        $this->_initResourceConfig();
        return $this;
    }

    /**
     * Dispatch REST request
     */
    public function dispatch()
    {
        try {
            // TODO: Introduce Authentication
            $role = $this->_authenticate($this->getRequest());
            $route = $this->_matchRoute($this->getRequest());

            $operation = $this->_getOperationName();
            $resourceVersion = $this->_getResourceVersion($operation);
            $method = $this->getResourceConfig()->getMethodNameByOperation($operation, $resourceVersion);
            $controllerClassName = $this->getResourceConfig()->getControllerClassByOperationName($operation);
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

            $this->_checkResourceAcl($role, $route->getResourceName(), $method);

            // TODO: Think about passing parameters if they will be available and valid in the resource action
            $inputData = $this->_presentation->fetchRequestData($controllerInstance, $action);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_presentation->prepareResponse($method, $outputData);
        } catch (Mage_Webapi_Exception $e) {
            $this->_addException($e);
        } catch (Exception $e) {
            if (!Mage::getIsDeveloperMode()) {
                Mage::logException($e);
                $this->_addException(new Mage_Webapi_Exception(
                    $this->_helper->__("Internal Error. Details are available in Magento log file."),
                    Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
                ));
            } else {
                $this->_addException($e);
            }
        }

        Mage::dispatchEvent('controller_front_send_response_before', array('front' => $this));
        Magento_Profiler::start('send_response');
        $this->_sendResponse();
        Magento_Profiler::stop('send_response');
        Mage::dispatchEvent('controller_front_send_response_after', array('front' => $this));
    }

    /**
     * Check whether current request match any route of specified method or not. Method version is taken into account.
     *
     * @param string $methodName
     * @param string $version
     * @throws Mage_Webapi_Exception In case when request does not match any route of specified method.
     */
    protected function _checkRoute($methodName, $version)
    {
        $resourceName = $this->getRequest()->getResourceName();
        $routes = $this->getResourceConfig()->getMethodRestRoutes($resourceName, $methodName, $version);
        foreach ($routes as $route) {
            if ($route->match($this->getRequest())) {
                return;
            }
        }
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
                    Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that matches current URL, set parameters of the route to Request object
     *
     * @param Mage_Webapi_Controller_Request_Rest $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _matchRoute(Mage_Webapi_Controller_Request_Rest $request)
    {
        $router = new Mage_Webapi_Controller_Router_Rest();
        $router->setRoutes($this->getResourceConfig()->getAllRestRoutes());
        $route = $router->match($request);
        /** Initialize additional request parameters using data from route */
        $this->getRequest()->setResourceName($route->getResourceName());
        $this->getRequest()->setResourceType($route->getResourceType());
        return $route;
    }

    /**
     * Identify operation name according to HTTP request parameters
     *
     * @return string
     * @throws Mage_Webapi_Exception
     */
    protected function _getOperationName()
    {
        // TODO: Add xsd validation of operations in resource.xml according to the following methods
        $restMethodsMap = array(
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_CREATE => 'create',
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_GET => 'list',
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_UPDATE => 'multiUpdate',
            self::ACTION_TYPE_COLLECTION . self::HTTP_METHOD_DELETE => 'multiDelete',
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_GET => 'get',
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_UPDATE => 'update',
            self::ACTION_TYPE_ITEM . self::HTTP_METHOD_DELETE => 'delete',
        );
        $httpMethod = $this->getRequest()->getHttpMethod();
        $resourceType = $this->getRequest()->getResourceType();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            throw new Mage_Webapi_Exception($this->_helper->__('Requested method does not exist.'),
                Mage_Webapi_Exception::HTTP_NOT_FOUND);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        $operationName = $this->getRequest()->getResourceName() . ucfirst($methodName);
        return $operationName;
    }

    /**
     * Authenticate user
     * @todo remove fake authentication code
     *
     * @throws Mage_Webapi_Exception
     * @param Mage_Webapi_Controller_RequestAbstract $request
     * @return string
     */
    protected function _authenticate(Mage_Webapi_Controller_RequestAbstract $request)
    {
        /** @var $collection Mage_Webapi_Model_Resource_Acl_User_Collection */
        $collection = Mage::getResourceModel('Mage_Webapi_Model_Resource_Acl_User_Collection');
        /** @var $user Mage_Webapi_Model_Acl_User */
        $user = $collection->getFirstItem();
        return $user->getRoleId();

//        try {
//            /** @var $oauthServer Mage_Oauth_Model_Server */
//            $oauthServer = Mage::getModel('Mage_Oauth_Model_Server', $request);
//            $consumerKey = $oauthServer->authenticateTwoLeggedRest();
//        } catch (Exception $e) {
//            TODO: Mage_Webapi_Exception must be translated
//            throw new Mage_Webapi_Exception($oauthServer->reportProblem($e), Mage_Webapi_Exception::HTTP_UNAUTHORIZED);
//        }
//        // TODO: implement consumer role loading
//        return $consumerKey;
    }

    /**
     * Redeclare custom shutdown function
     *
     * @param   string $handler
     * @return  Mage_Webapi_Controller_Front_Rest
     */
    public function registerShutdownFunction($handler)
    {
        register_shutdown_function($handler);
        return $this;
    }

    /**
     * Send response to the client, render exceptions if present
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
            // This could happen in renderer factory. Tunnelling of 406(Not acceptable) error
            $httpCode = $e->getCode() == Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                ? Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;

            //if error appeared in "error rendering" process then use error renderer
            $this->_renderInternalError($e->getMessage(), $e->getTraceAsString(), $httpCode);
        }
    }

    /**
     * Process application error
     * Create report if not in developer mode and render error to send correct api response
     *
     * @param string $errorMessage detailed error message
     * @param string $trace exception trace
     * @param int|null $httpCode
     */
    protected function _renderInternalError($errorMessage, $trace = 'Trace is not available.', $httpCode = null)
    {
        $processor = new Mage_Webapi_Controller_Front_Rest_ErrorProcessor();
        if (!Mage::getIsDeveloperMode()) {
            $processor->saveReport($errorMessage . $trace);
        }
        $processor->render($errorMessage, $trace, $httpCode);
    }

    /**
     * Generate and set HTTP response code, error messages to Response object
     */
    protected function _renderMessages()
    {
        $response = $this->getResponse();
        $formattedMessages = array();
        $formattedMessages['messages'] = $response->getMessages();
        $lastExceptionHttpCode = null;
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
            $lastExceptionHttpCode = $code;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $response->setHttpResponseCode($lastExceptionHttpCode);
        $response->setMimeType($this->_getRenderer()->getMimeType());
        $response->setBody($this->_getRenderer()->render($formattedMessages));
        return $this;
    }

    /**
     * Get renderer object according to request accepted mime type
     *
     * @return Mage_Webapi_Controller_Response_RendererInterface
     */
    protected function _getRenderer()
    {
        if (!$this->_renderer) {
            $this->_renderer = Mage_Webapi_Controller_Response_Renderer::factory($this->getRequest()->getAcceptTypes());
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
     * Function to catch errors, not catched by the user error handler function
     */
    public function mageApiShutdownFunction()
    {
        $E_FATAL = E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR;

        $error = error_get_last();

        if ($error && ($error['type'] & $E_FATAL)) {
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
                trigger_error("'" . $errorMessage . "'", E_USER_ERROR);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }

            $this->_renderInternalError($errorMessage);
        }
    }
}
