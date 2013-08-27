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
    const REQUEST_TYPE = 'soap';

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Mage_Webapi_Controller_Soap_Request */
    protected $_request;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Webapi_Controller_Soap_Handler */
    protected $_soapHandler;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Soap_Request $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Mage_Webapi_Model_Soap_Server $soapServer
     * @param Mage_Webapi_Model_Soap_Fault $soapFault
     * @param Mage_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Mage_Webapi_Controller_Soap_Handler $soapHandler
     */
    public function __construct(
        Mage_Webapi_Controller_Soap_Request $request,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Mage_Webapi_Model_Soap_Server $soapServer,
        Mage_Webapi_Model_Soap_Fault $soapFault,
        Mage_Webapi_Controller_ErrorProcessor $errorProcessor,
        Mage_Webapi_Controller_Soap_Handler $soapHandler
    ) {
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
        $this->_response = $response;
        $this->_errorProcessor = $errorProcessor;
        $this->_soapHandler = $soapHandler;
    }

    /**
     * Initialize front controller
     *
     * @return Mage_Webapi_Controller_Soap
     */
    public function init()
    {
        ini_set('display_startup_errors', 0);
        ini_set('display_errors', 0);

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
            if ($this->_request->getParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null) {
                $responseBody = $this->_autoDiscover->handle(
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
            $maskedException = $this->_errorProcessor->maskException($e);
            $this->_processBadRequest($maskedException->getMessage());
        }

        $this->_response->sendResponse();
        return $this;
    }

    /**
     * Process request as HTTP 400 and set error message.
     *
     * @param string $message
     */
    protected function _processBadRequest($message)
    {
        $this->_setResponseContentType('text/xml');
        $this->_response->setHttpResponseCode(400);
        $details = array();
        // TODO: Generate list of available URLs when invalid WSDL URL specified
        $this->_setResponseBody(
            $this->_soapFault->getSoapFaultMessage(
                $message,
                Mage_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER,
                $this->_soapFault->getLanguage(),
                $details
            )
        );
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
        $this->_soapServer->initWsdlCache();
        $this->_soapServer->setWSDL($this->_soapServer->generateUri(true))
            ->setEncoding($this->_soapServer->getApiCharset())
            ->setSoapVersion(SOAP_1_2);
        use_soap_error_handler(false);
        // TODO: Headers are not available at this point.
        // $this->_soapHandler->setRequestHeaders($this->_getRequestHeaders());
        $this->_soapServer->setReturnResponse(true)->setObject($this->_soapHandler);

        return $this->_soapServer;
    }
}
