<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 Server
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Server
{
    /**
     * Api2 REST type
     */
    const API_TYPE_REST = 'rest';

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

    const DEFAULT_SHUTDOWN_FUNCTION = 'mageApiShutdownFunction';

    /**
     * List of api types
     *
     * @var array
     */
    protected static $_apiTypes = array(self::API_TYPE_REST);

    /**
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_authUser;

    /**
     * @var Mage_Api2_Model_Renderer_Interface
     */
    protected $_renderer;

    /**
     * Initialize server errors processing mechanism
     *
     * @return Mage_Api2_Model_Server
     */
    protected function _initEnvironment()
    {
        // make sure all errors will not be displayed
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);

        // redeclare custom shutdown function to handle fatal errors correctly
        $this->registerShutdownFunction(array($this, self::DEFAULT_SHUTDOWN_FUNCTION));

        return $this;
    }

    /**
     * Run server
     */
    public function run()
    {
        $this->_initEnvironment();

        try {
            $request = $this->_getRequest();
            $response = $this->_getResponse();
            $apiUser = $this->_authenticate($request);

            $this->_route($request)
                ->_allow($request, $apiUser)
                ->_dispatch($request, $response, $apiUser);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_addException($e);
        }

        $this->_sendResponse();
    }

    /**
     * Add exception to response
     *
     * @param Exception $exception
     * @return Mage_Api2_Model_Server
     */
    protected function _addException(Exception $exception)
    {
        $response = $this->_getResponse();
        $response->setException($exception);
        return $this;
    }

    /**
     * Send response to the client, render exceptions if present
     */
    protected function _sendResponse()
    {
        try {
            if ($this->_getResponse()->isException()) {
                $this->_renderMessages();
            }
            $this->_getResponse()->sendResponse();
        } catch (Exception $e) {
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            // This could happen in renderer factory. Tunnelling of 406(Not acceptable) error
            $httpCode = $e->getCode() == self::HTTP_NOT_ACCEPTABLE
                    ? self::HTTP_NOT_ACCEPTABLE
                    : self::HTTP_INTERNAL_ERROR;

            //if error appeared in "error rendering" process then use error renderer
            $this->_renderInternalError($e->getMessage() . PHP_EOL . $e->getTraceAsString(), $httpCode);
        }
    }

    /**
     * Make internal call to api
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @return Mage_Api2_Model_Response
     */
    public function internalCall(Mage_Api2_Model_Request $request, Mage_Api2_Model_Response $response)
    {
        $apiUser = $this->_getAuthUser();
        $this->_route($request)
            ->_allow($request, $apiUser)
            ->_dispatch($request, $response, $apiUser);
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
     * @return Mage_Api2_Model_Server
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
            throw new Exception("Mage_Api2_Model_Server::internalCall() seems to be executed "
                . "before Mage_Api2_Model_Server::run()");
        }
        return $this->_authUser;
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Server
     */
    protected function _route(Mage_Api2_Model_Request $request)
    {
        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getModel('Mage_Api2_Model_Router');

        $router->routeApiType($request, true)
            ->setRoutes($this->_getConfig()->getRoutes($request->getApiType()))
            ->match($request);

        return $this;
    }

    /**
     * Global ACL processing
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Mage_Api2_Model_Server
     * @throws Mage_Api2_Exception
     */
    protected function _allow(Mage_Api2_Model_Request $request, Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        /** @var $globalAcl Mage_Api2_Model_Acl_Global */
        $globalAcl = Mage::getModel('Mage_Api2_Model_Acl_Global');

        if (!$globalAcl->isAllowed($apiUser, $request->getResourceName(), $request->getActionName())) {
            throw new Mage_Api2_Exception('Access denied', self::HTTP_FORBIDDEN);
        }
        return $this;
    }

    /**
     * Load class file, instantiate resource class, set parameters to the instance, run resource internal dispatch
     * method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Mage_Api2_Model_Server
     */
    protected function _dispatch(
        Mage_Api2_Model_Request $request,
        Mage_Api2_Model_Response $response,
        Mage_Api2_Model_Auth_User_Abstract $apiUser
    )
    {
        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('Mage_Api2_Model_Dispatcher');
        $dispatcher->setApiUser($apiUser)->dispatch($request, $response);

        return $this;
    }

    /**
     * Get request object
     *
     * @return Mage_Api2_Model_Request
     */
    protected function _getRequest()
    {
        return Mage::getSingleton('Mage_Api2_Model_Request');
    }

    /**
     * Get response object
     *
     * @return Mage_Api2_Model_Response
     */
    protected function _getResponse()
    {
        return Mage::getSingleton('Mage_Api2_Model_Response');
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
            $request = $this->_getRequest();
            $this->_renderer = Mage_Api2_Model_Renderer::factory($request->getAcceptTypes());
        }
        return $this->_renderer;
    }

    /**
     * Get api2 config instance
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getModel('Mage_Api2_Model_Config');
    }

    /**
     * Generate and set HTTP response code, error messages to Response object
     *
     * @return Mage_Api2_Model_Server
     */
    protected function _renderMessages()
    {
        $response = $this->_getResponse();
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
                $code = self::HTTP_INTERNAL_ERROR;
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
     * Retrieve api types
     *
     * @return array
     */
    public static function getApiTypes()
    {
        return self::$_apiTypes;
    }

    /**
     * Redeclare custom shutdown function
     *
     * @param   string $handler
     * @return  Mage_Api2_Model_Server
     */
    public function registerShutdownFunction($handler)
    {
        register_shutdown_function($handler);
        return $this;
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
            switch($error['type']){
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
}
