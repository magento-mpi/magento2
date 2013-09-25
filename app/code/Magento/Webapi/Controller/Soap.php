<?php
/**
 * Front controller for WebAPI SOAP area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Soap implements Magento_Core_Controller_FrontInterface
{
    /**#@+
     * Content types used for responses processed by SOAP web API.
     */
    const CONTENT_TYPE_SOAP_CALL = 'application/soap+xml';
    const CONTENT_TYPE_WSDL_REQUEST = 'text/xml';
    /**#@-*/

    /** @var Magento_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Magento_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /** @var Magento_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Magento_Webapi_Controller_Response */
    protected $_response;

    /** @var Magento_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Core_Model_App_State */
    protected $_appState;

    /** @var Magento_Core_Model_App */
    protected $_application;

    /** @var Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_oauthHelper;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Soap_Request $request
     * @param Magento_Webapi_Controller_Response $response
     * @param Magento_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator
     * @param Magento_Webapi_Model_Soap_Server $soapServer
     * @param Magento_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_App $application
     * @param Magento_Oauth_Service_OauthV1Interface $oauthService
     * @param Magento_Oauth_Helper_Data $oauthHelper
     */
    public function __construct(
        Magento_Webapi_Controller_Soap_Request $request,
        Magento_Webapi_Controller_Response $response,
        Magento_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator,
        Magento_Webapi_Model_Soap_Server $soapServer,
        Magento_Webapi_Controller_ErrorProcessor $errorProcessor,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_App $application,
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Oauth_Helper_Data $oauthHelper
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_wsdlGenerator = $wsdlGenerator;
        $this->_soapServer = $soapServer;
        $this->_errorProcessor = $errorProcessor;
        $this->_appState = $appState;
        $this->_application = $application;
        $this->_oauthService = $oauthService;
        $this->_oauthHelper = $oauthHelper;
    }

    /**
     * Initialize front controller
     *
     * @return Magento_Webapi_Controller_Soap
     */
    public function init()
    {
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Magento_Webapi_Controller_Soap
     */
    public function dispatch()
    {
        try {
            if (!$this->_appState->isInstalled()) {
                throw new Magento_Webapi_Exception(__('Magento is not yet installed'));
            }
            if ($this->_isWsdlRequest()) {
                $responseBody = $this->_wsdlGenerator->generate(
                    $this->_request->getRequestedServices(),
                    $this->_soapServer->generateUri()
                );
                $this->_setResponseContentType(self::CONTENT_TYPE_WSDL_REQUEST);
            } else {
                $this->_oauthService->validateAccessToken($this->_getAccessToken());
                $responseBody = $this->_soapServer->handle();
                $this->_setResponseContentType(self::CONTENT_TYPE_SOAP_CALL);
            }
            $this->_setResponseBody($responseBody);
        } catch (Exception $e) {
            $this->_prepareErrorResponse($e);
        }
        $this->_response->sendResponse();
        return $this;
    }

    /**
     * Check if current request is WSDL request. SOAP operation execution request is another type of requests.
     *
     * @return bool
     */
    protected function _isWsdlRequest()
    {
        return $this->_request->getParam(Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null;
    }

    /**
     * Parse the Authorization header and return the access token
     * eg Authorization: Bearer <access-token>
     *
     * @return string Access token
     */
    protected function _getAccessToken()
    {
        $token = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
        return $token[1];
    }

    /**
     * Set body and status code to response using information extracted from provided exception.
     *
     * @param Exception $exception
     */
    protected function _prepareErrorResponse($exception)
    {
        $maskedException = $this->_errorProcessor->maskException($exception);
        $soapFault = new Magento_Webapi_Model_Soap_Fault($this->_application, $maskedException);
        if ($this->_isWsdlRequest()) {
            $httpCode = $maskedException->getHttpCode();
            $contentType = self::CONTENT_TYPE_WSDL_REQUEST;
        } else {
            $httpCode = Magento_Webapi_Controller_Response::HTTP_OK;
            $contentType = self::CONTENT_TYPE_SOAP_CALL;
        }
        $this->_setResponseContentType($contentType);
        $this->_response->setHttpResponseCode($httpCode);
        // TODO: Generate list of available URLs when invalid WSDL URL specified
        $this->_setResponseBody($soapFault->toXml());
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Magento_Webapi_Controller_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->_response->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_soapServer->getApiCharset()}");
        return $this;
    }

    /**
     * Replace WSDL xml encoding from config, if present, else default to UTF-8 and set it to the response object.
     *
     * @param string $responseBody
     * @return Magento_Webapi_Controller_Soap
     */
    protected function _setResponseBody($responseBody)
    {
        $this->_response->setBody(
            preg_replace(
                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                '<?xml version="$1" encoding="' . $this->_soapServer->getApiCharset() . '"?>',
                $responseBody
            )
        );
        return $this;
    }
}
