<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front controller for REST API
 */
// TODO: Add profiler calls
class Mage_Webapi_Controller_Front_Rest extends Mage_Webapi_Controller_FrontAbstract
{
    const BASE_ACTION_CONTROLLER = 'Mage_Webapi_Controller_Rest_ActionAbstract';

    /**#@+
     * Version limits
     */
    const VERSION_MIN = 1;
    const VERSION_MAX = 200;
    /**#@-*/

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK                 = 200;
    const HTTP_CREATED            = 201;
    const HTTP_MULTI_STATUS       = 207;
    const HTTP_BAD_REQUEST        = 400;
    const HTTP_UNAUTHORIZED       = 401;
    const HTTP_FORBIDDEN          = 403;
    const HTTP_NOT_FOUND          = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE     = 406;
    const HTTP_INTERNAL_ERROR     = 500;
    /**#@- */

    /**#@+
     * Resource types
     */
    const RESOURCE_TYPE_ITEM = 'item';
    const RESOURCE_TYPE_COLLECTION = 'collection';
    /**#@-*/

    /**#@+
     * HTTP methods supported by REST
     */
    const HTTP_METHOD_CREATE = 'create';
    const HTTP_METHOD_RETRIEVE = 'retrieve';
    const HTTP_METHOD_UPDATE = 'update';
    const HTTP_METHOD_DELETE = 'delete';
    /**#@-*/

    /**#@+
     *  Default error messages
     */
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data invalid.'; //error while checking data inside method
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    /**#@-*/

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR       = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_PAGING_LIMIT_ERROR = 'The paging limit exceeds the allowed number.';
    const RESOURCE_COLLECTION_ORDERING_ERROR     = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR    = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR   = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**#@+
     *  Default success messages
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource updated successful.';
    /**#@-*/


    const DEFAULT_SHUTDOWN_FUNCTION = 'mageApiShutdownFunction';

    /** @var string */
    protected $_baseActionController;

    /**
     * @var Mage_Webapi_Model_Renderer_Interface
     */
    protected $_renderer;

    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_restConfig;

    /** @var Mage_Webapi_Controller_Front_Rest_Presentation */
    protected $_presentation;

    /**
     * Extend parent with REST specific config initialization and server errors processing mechanism initialization
     *
     * @return Mage_Webapi_Controller_Front_Rest|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        $this->_baseActionController = self::BASE_ACTION_CONTROLLER;
        $configFiles = Mage::getConfig()->getModuleConfigurationFiles('api_rest.xml');
        /** @var Mage_Webapi_Model_Config_Rest $restConfig */
        $restConfig = Mage::getModel('Mage_Webapi_Model_Config_Rest', $configFiles);
        $this->setRestConfig($restConfig);

        // redeclare custom shutdown function to handle fatal errors correctly
        $this->registerShutdownFunction(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        $this->_presentation = Mage::getModel('Mage_Webapi_Controller_Front_Rest_Presentation', $this);
        return $this;
    }

    /**
     * Dispatch REST request
     */
    public function dispatch()
    {
        try {
        // TODO: Introduce Authentication and Authorization
//            $role = $this->_authenticate($this->getRequest());

            $route = $this->_matchRoute($this->getRequest());
            $this->getRequest()->setResourceName($route->getResourceName());
            $this->getRequest()->setResourceType($route->getResourceType());
//            $this->_checkResourceAcl($role, $route->getResourceName());

            $controllerClassName = $this->getRestConfig()->getControllerClassByResourceName($route->getResourceName());
            $controllerInstance = $this->_getActionControllerInstance($controllerClassName);
            $method = $this->_getMethodName($route->getResourceType());
            // TODO: Think about passing parameters if they will be available and valid in the resource action
            $action = $method . $this->_getAvailableMethodSuffix($method, $controllerInstance);

            $inputData = $this->_presentation->fetchRequestData($method);
            $outputData = call_user_func_array(array($controllerInstance, $action), $inputData);
            $this->_presentation->prepareResponse($method, $outputData);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_addException($e);
        }

        Mage::dispatchEvent('controller_front_send_response_before', array('front' => $this));
        Magento_Profiler::start('send_response');
        $this->_sendResponse();
        Magento_Profiler::stop('send_response');
        Mage::dispatchEvent('controller_front_send_response_after', array('front' => $this));
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that matches current URL, set parameters of the route to Request object
     *
     * @param Mage_Webapi_Model_Request $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     */
    protected function _matchRoute(Mage_Webapi_Model_Request $request)
    {
        $router = new Mage_Webapi_Controller_Router_Rest();
        $router->setRoutes($this->getRestConfig()->getRoutes());
        return $router->match($request);
    }

    /**
     * Identify required action name based on HTTP request parameters
     *
     * @param string $resourceType
     * @return string
     */
    protected function _getMethodName($resourceType)
    {
        $restMethodsMap = array(
            self::RESOURCE_TYPE_COLLECTION . self::HTTP_METHOD_CREATE => 'create',
            self::RESOURCE_TYPE_COLLECTION . self::HTTP_METHOD_RETRIEVE => 'multiGet',
            self::RESOURCE_TYPE_COLLECTION . self::HTTP_METHOD_UPDATE => 'multiUpdate',
            self::RESOURCE_TYPE_COLLECTION . self::HTTP_METHOD_DELETE => 'multiDelete',
            self::RESOURCE_TYPE_ITEM . self::HTTP_METHOD_RETRIEVE => 'get',
            self::RESOURCE_TYPE_ITEM . self::HTTP_METHOD_UPDATE => 'update',
            self::RESOURCE_TYPE_ITEM . self::HTTP_METHOD_DELETE => 'delete',
        );
        /** @var Mage_Webapi_Model_Request $request */
        $request = $this->getRequest();
        $httpMethod = $request->getHttpMethod();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            Mage::helper('Mage_Webapi_Helper_Rest')->critical(Mage_Webapi_Helper_Rest::RESOURCE_METHOD_NOT_ALLOWED);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        return $methodName;
    }

    /**
     * Find the most appropriate version suffix for the requested action.
     *
     * If there is no action with requested version, fallback mechanism is used.
     * If there is no appropriate action found after fallback - exception is thrown.
     *
     * @param string $methodName
     * @param Mage_Webapi_Controller_Rest_ActionAbstract $controllerInstance
     * @return string
     * @throws Mage_Webapi_Exception|Exception
     */
    protected function _getAvailableMethodSuffix($methodName, $controllerInstance)
    {
        $methodVersion = $this->_getVersion();
        while ($methodVersion >= self::VERSION_MIN) {
            $methodSuffix = 'V' . $methodVersion;
            if ($controllerInstance->hasAction($methodName . $methodSuffix)) {
                return $methodSuffix;
            }
            $methodVersion--;
        }
        // TODO: Think about better messages
        Mage::helper('Mage_Webapi_Helper_Rest')->critical(Mage_Webapi_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Get correct version of the resource model
     *
     * @return int
     * @throws Mage_Webapi_Exception
     */
    protected function _getVersion()
    {
//        /** @var Mage_Webapi_Model_Request $request */
//        $request = $this->getRequest();
//        $requestedVersion = $request->getVersion();
//        if (false !== $requestedVersion && !preg_match('/^[1-9]\d*$/', $requestedVersion)) {
//            throw new Mage_Webapi_Exception(
//                sprintf('Invalid version "%s" requested.', htmlspecialchars($requestedVersion)),
//                Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST
//            );
//        }
        // TODO: Implement versioning
        $version = 1;
        if ($version > self::VERSION_MAX) {
            Mage::helper('Mage_Webapi_Helper_Rest')->critical(
                Mage::helper('Mage_Webapi_Helper_Data')->__("Resource version cannot exceed %s", self::VERSION_MAX),
                Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR
            );
        }
        return (int)$version;
    }

    /**
     * Set config for REST.
     *
     * @param Mage_Webapi_Model_Config_Rest $config
     * @return Mage_Webapi_Controller_Front_Rest
     */
    public function setRestConfig(Mage_Webapi_Model_Config_Rest $config)
    {
        $this->_restConfig = $config;
        return $this;
    }

    /**
     * Retrieve REST specific config
     *
     * @return Mage_Webapi_Model_Config_Rest
     */
    public function getRestConfig()
    {
        return $this->_restConfig;
    }

    /**
     * Authenticate user
     *
     * @throws Mage_Webapi_Exception
     * @param Mage_Webapi_Model_Request $request
     * @return string
     */
    protected function _authenticate(Mage_Webapi_Model_Request $request)
    {
        try {
            /** @var $oauthServer Mage_Oauth_Model_Server */
            $oauthServer = Mage::getModel('Mage_Oauth_Model_Server', $request);
            $consumerKey = $oauthServer->authenticateTwoLeggedRest();
        } catch (Exception $e) {
            throw new Mage_Webapi_Exception($oauthServer->reportProblem($e), Mage_Webapi_Controller_Front_Rest::HTTP_UNAUTHORIZED);
        }
        // TODO: implement consumer role loading
        return $consumerKey;
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
            $httpCode = $e->getCode() == Mage_Webapi_Controller_Front_Rest::HTTP_NOT_ACCEPTABLE
                ? Mage_Webapi_Controller_Front_Rest::HTTP_NOT_ACCEPTABLE
                : Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR;

            //if error appeared in "error rendering" process then use error renderer
            $this->_renderInternalError($e->getMessage() . PHP_EOL . $e->getTraceAsString(), $httpCode);
        }
    }

    /**
     * Process application error
     * Create report if not in developer mode and render error to send correct api response
     *
     * @param string $detailedErrorMessage detailed error message
     * @param int|null $httpCode
     */
    protected function _renderInternalError($detailedErrorMessage, $httpCode = null)
    {
        $processor = new Mage_Webapi_Model_Error_Processor();
        if (!Mage::getIsDeveloperMode()) {
            $processor->saveReport($detailedErrorMessage);
        }
        $processor->render($detailedErrorMessage, $httpCode);
    }

    /**
     * Generate and set HTTP response code, error messages to Response object
     *
     * @return Mage_Webapi_Model_Server
     */
    protected function _renderMessages()
    {
        $response = $this->getResponse();
        $formattedMessages = array();
        $formattedMessages['messages'] = $response->getMessages();
        $lastExceptionHttpCode = null;
        /** @var Exception $exception */
        foreach ($response->getException() as $exception) {
            if ($exception instanceof Mage_Webapi_Exception) {
                $code = $exception->getCode();
                $message = $exception->getMessage();
                $trace = $exception->getTraceAsString();
            } else {
                $code = Mage_Webapi_Controller_Front_Rest::HTTP_INTERNAL_ERROR;
                $message = Mage_Webapi_Controller_Front_Rest::RESOURCE_INTERNAL_ERROR;
                $trace = $exception->getMessage() . PHP_EOL . $exception->getTraceAsString();
            }
            $messageData = array('code' => $code, 'message' => $message);
            if (Mage::getIsDeveloperMode()) {
                $messageData['trace'] = $trace;
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
     * @return Mage_Webapi_Model_Renderer_Interface
     */
    protected function _getRenderer()
    {
        if (!$this->_renderer) {
            /** @var $request Mage_Webapi_Model_Request */
            $request = $this->getRequest();
            $this->_renderer = Mage_Webapi_Model_Renderer::factory($request->getAcceptTypes());
        }
        return $this->_renderer;
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
