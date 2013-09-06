<?php
/**
 * Front controller for WebAPI SOAP area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Soap implements Mage_Core_Controller_FrontInterface
{
    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Core_Model_App_State */
    protected $_appState;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Soap_Request $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator
     * @param Mage_Webapi_Model_Soap_Server $soapServer
     * @param Mage_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Mage_Core_Model_App_State $appState
     * @param Mage_Core_Model_App $application
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(
        Mage_Webapi_Controller_Soap_Request $request,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Model_Soap_Wsdl_Generator $wsdlGenerator,
        Mage_Webapi_Model_Soap_Server $soapServer,
        Mage_Webapi_Controller_ErrorProcessor $errorProcessor,
        Mage_Core_Model_App_State $appState,
        Mage_Core_Model_App $application,
        Mage_Webapi_Helper_Data $helper
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_wsdlGenerator = $wsdlGenerator;
        $this->_soapServer = $soapServer;
        $this->_errorProcessor = $errorProcessor;
        $this->_appState = $appState;
        $this->_application = $application;
        $this->_helper = $helper;
    }

    /**
     * Initialize front controller
     *
     * @return Mage_Webapi_Controller_Soap
     */
    public function init()
    {
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Soap
     */
    public function dispatch()
    {
        try {
            if (!$this->_appState->isInstalled()) {
                throw new Mage_Webapi_Exception(
                    $this->_helper->__('Magento is not yet installed'),
                    Mage_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }
            if ($this->_isWsdlRequest()) {
                $responseBody = $this->_wsdlGenerator->generate(
                    $this->_request->getRequestedServices(),
                    $this->_soapServer->generateUri()
                );
                $this->_setResponseContentType('text/xml');
            } else {
                $responseBody = $this->_initSoapServer()->handle();
                $this->_setResponseContentType('application/soap+xml');
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
        return $this->_request->getParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null;
    }

    /**
     * Set body and status code to response using information extracted from provided exception.
     *
     * @param Mage_Webapi_Exception $exception
     */
    protected function _prepareErrorResponse($exception)
    {
        $maskedException = $this->_errorProcessor->maskException($exception);
        $this->_setResponseContentType('text/xml');
        $soapFault = new Mage_Webapi_Model_Soap_Fault($this->_application, $maskedException);
        $httpCode = $this->_isWsdlRequest()
            ? $maskedException->getHttpCode()
            : Mage_Webapi_Controller_Rest_Response::HTTP_OK;
        $this->_response->setHttpResponseCode($httpCode);
        // TODO: Generate list of available URLs when invalid WSDL URL specified
        $this->_setResponseBody($soapFault->toXml());
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Soap
     */
    protected function _setResponseContentType($contentType = 'text/xml')
    {
        $this->_response->clearHeaders()
            ->setHeader('Content-Type', "$contentType; charset={$this->_soapServer->getApiCharset()}");
        return $this;
    }

    /**
     * Set body to response object.
     *
     * @param string $responseBody
     * @return Mage_Webapi_Controller_Soap
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

    /**
     * Initialize SOAP Server.
     *
     * @return Mage_Webapi_Model_Soap_Server
     */
    protected function _initSoapServer()
    {
        use_soap_error_handler(false);
        // TODO: Headers are not available at this point.
        // $this->_soapHandler->setRequestHeaders($this->_getRequestHeaders());

        return $this->_soapServer;
    }
}
