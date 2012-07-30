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
class Mage_Api2_Controller_Front_Rest extends Mage_Api2_Controller_FrontAbstract
{
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

    const DEFAULT_METHOD_VERSION = 1;

    const DEFAULT_SHUTDOWN_FUNCTION = 'mageApiShutdownFunction';

    // TODO: Take base controller from configuration
    /** @var string */
    protected $_baseActionController = 'Mage_Api2_Controller_Rest_ActionAbstract';

    /**
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_authUser;

    /**
     * @var Mage_Api2_Model_Renderer_Interface
     */
    protected $_renderer;

    /** @var Mage_Api2_Model_Config_Rest */
    protected $_restConfig;

    /**
     * Extend parent with REST specific config initialization and server errors processing mechanism initialization
     *
     * @return Mage_Api2_Controller_Front_Rest|Mage_Core_Controller_FrontInterface
     */
    public function init()
    {
        parent::init();
        $configFiles = Mage::getConfig()->getModuleConfigurationFiles('api_rest.xml');
        /** @var Mage_Api2_Model_Config_Rest $restConfig */
        $restConfig = Mage::getModel('Mage_Api2_Model_Config_Rest', $configFiles);
        $this->setRestConfig($restConfig);

        // redeclare custom shutdown function to handle fatal errors correctly
        $this->registerShutdownFunction(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));
        return $this;
    }

    /**
     * Dispatch REST request
     */
    public function dispatch()
    {
        try {
            $route = $this->_matchRoute($this->getRequest());
            $this->_checkResourceAcl();

            $controllerClassName = $this->getRestConfig()->getControllerClassByResourceName($route->getResourceName());
            $controllerInstance = $this->_getActionControllerInstance($controllerClassName);
            $action = $this->_getActionName($route->getResourceType());
            if (!$controllerInstance->hasAction($action)) {
                // TODO: Think about better messages
                Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_NOT_FOUND);
            }
            // TODO: Think about passing parameters if they will be available and valid in the resource action
            $controllerInstance->$action();
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
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Controller_Router_Route_Rest
     */
    protected function _matchRoute(Mage_Api2_Model_Request $request)
    {
        $router = new Mage_Api2_Controller_Router_Rest();
        $route = $router->setRoutes($this->getRestConfig()->getRoutes())->match($request);
        return $route;
    }

    /**
     * Identify required action name based on HTTP request parameters
     *
     * @param string $resourceType
     * @return string
     */
    protected function _getActionName($resourceType)
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
        /** @var Mage_Api2_Model_Request $request */
        $request = $this->getRequest();
        $httpMethod = $request->getHttpMethod();
        if (!isset($restMethodsMap[$resourceType . $httpMethod])) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_ALLOWED);
        }
        $methodName = $restMethodsMap[$resourceType . $httpMethod];
        $methodVersion = ($this->_getVersion() != self::DEFAULT_METHOD_VERSION) ? $this->_getVersion() : '';
        return $methodName . $methodVersion;
    }

    /**
     * Get correct version of the resource model
     *
     * @return int
     * @throws Mage_Api2_Exception
     */
    protected function _getVersion()
    {
        /** @var Mage_Api2_Model_Request $request */
        $request = $this->getRequest();
        $requestedVersion = $request->getVersion();
        if (false !== $requestedVersion && !preg_match('/^[1-9]\d*$/', $requestedVersion)) {
            throw new Mage_Api2_Exception(
                sprintf('Invalid version "%s" requested.', htmlspecialchars($requestedVersion)),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }
        // TODO: Implement versioning
        return '';
//        return $this->_getConfig()->getResourceLastVersion($this->getRequest()->getResourceType(), $requestedVersion);
    }

    /**
     * Set config for REST.
     *
     * @param Mage_Api2_Model_Config_Rest $config
     * @return Mage_Api2_Controller_Front_Rest
     */
    public function setRestConfig(Mage_Api2_Model_Config_Rest $config)
    {
        $this->_restConfig = $config;
        return $this;
    }

    /**
     * Retrieve REST specific config
     *
     * @return Mage_Api2_Model_Config_Rest
     */
    public function getRestConfig()
    {
        return $this->_restConfig;
    }

    /**
     * Authenticate user
     *
     * @throws Exception
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('Mage_Api2_Model_Auth');

        $this->_setAuthUser($authManager->authenticate($request));
        return $this->_getAuthUser();
    }

    /**
     * Set auth user
     *
     * @throws Exception
     * @param Mage_Api2_Model_Auth_User_Abstract $authUser
     * @return Mage_Api2_Controller_Front_Rest
     */
    protected function _setAuthUser(Mage_Api2_Model_Auth_User_Abstract $authUser)
    {
        $this->_authUser = $authUser;
        return $this;
    }

    /**
     * Retrieve existing auth user
     *
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _getAuthUser()
    {
        if (!$this->_authUser) {
            throw new Exception("Auth User is not initialized.");
        }
        return $this->_authUser;
    }

    /**
     * Re-declare custom shutdown function
     *
     * @param   string $handler
     * @return  Mage_Api2_Controller_Front_Rest
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
            $httpCode = $e->getCode() == Mage_Api2_Model_Server::HTTP_NOT_ACCEPTABLE
                ? Mage_Api2_Model_Server::HTTP_NOT_ACCEPTABLE
                : Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR;

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
        $processor = new Mage_Api2_Model_Error_Processor();
        if (!Mage::getIsDeveloperMode()) {
            $processor->saveReport($detailedErrorMessage);
        }
        $processor->render($detailedErrorMessage, $httpCode);
    }

    /**
     * Generate and set HTTP response code, error messages to Response object
     *
     * @return Mage_Api2_Model_Server
     */
    protected function _renderMessages()
    {
        $response = $this->getResponse();
        $formattedMessages = array();
        $formattedMessages['messages'] = $response->getMessages();
        $lastExceptionHttpCode = null;
        /** @var Exception $exception */
        foreach ($response->getException() as $exception) {
            if ($exception instanceof Mage_Api2_Exception) {
                $code = $exception->getCode();
                $message = $exception->getMessage();
                $trace = $exception->getTraceAsString();
            } else {
                $code = Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR;
                $message = Mage_Api2_Model_Resource::RESOURCE_INTERNAL_ERROR;
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
        // set HTTP Code of last error, Content-Type and all rendered error messages to body
        $response->setHttpResponseCode($lastExceptionHttpCode);
        $response->setMimeType($this->_getRenderer()->getMimeType());
        $response->setBody($this->_getRenderer()->render($formattedMessages));
        return $this;
    }

    /**
     * Get renderer object according to request accepted mime type
     *
     * @return Mage_Api2_Model_Renderer_Interface
     */
    protected function _getRenderer()
    {
        if (!$this->_renderer) {
            /** @var $request Mage_Api2_Model_Request */
            $request = $this->getRequest();
            $this->_renderer = Mage_Api2_Model_Renderer::factory($request->getAcceptTypes());
        }
        return $this->_renderer;
    }

    /**
     * Make internal call to api
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Model_Response
     */
    // TODO: Think how to implement internal call according to the new architecture
    // TODO: Currently is used in Mage_Api2_Model_Multicall::_internalCall()
    public function internalCall(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
    }

    /**
     * Function to catch no user error handler function errors
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
