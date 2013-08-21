<?php
/**
 * Dispatcher for SOAP API calls.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Soap implements Magento_Webapi_Controller_DispatcherInterface
{
    /** @var Magento_Webapi_Model_Config_Soap */
    protected $_apiConfig;

    /** @var Magento_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Magento_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Magento_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Magento_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /** @var Magento_Webapi_Controller_Response */
    protected $_response;

    /** @var Magento_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Webapi_Controller_Dispatcher_Soap_Handler */
    protected $_soapHandler;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Soap $apiConfig
     * @param Magento_Webapi_Controller_Request_Soap $request
     * @param Magento_Webapi_Controller_Response $response
     * @param Magento_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Magento_Webapi_Model_Soap_Server $soapServer
     * @param Magento_Webapi_Model_Soap_Fault $soapFault
     * @param Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Magento_Webapi_Controller_Dispatcher_Soap_Handler $soapHandler
     */
    public function __construct(
        Magento_Webapi_Model_Config_Soap $apiConfig,
        Magento_Webapi_Controller_Request_Soap $request,
        Magento_Webapi_Controller_Response $response,
        Magento_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Magento_Webapi_Model_Soap_Server $soapServer,
        Magento_Webapi_Model_Soap_Fault $soapFault,
        Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Magento_Webapi_Controller_Dispatcher_Soap_Handler $soapHandler
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
        $this->_response = $response;
        $this->_errorProcessor = $errorProcessor;
        $this->_soapHandler = $soapHandler;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Magento_Webapi_Controller_Dispatcher_Soap
     */
    public function dispatch()
    {
        try {
            if ($this->_request->getParam(Magento_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null) {
                $responseBody = $this->_autoDiscover->handle(
                    $this->_request->getRequestedResources(),
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
        foreach ($this->_apiConfig->getAllResourcesVersions() as $resourceName => $versions) {
            foreach ($versions as $version) {
                $details['availableResources'][$resourceName][$version] = sprintf(
                    '%s?wsdl&resources[%s]=%s',
                    $this->_soapServer->getEndpointUri(),
                    $resourceName,
                    $version
                );
            }
        }

        $this->_setResponseBody(
            $this->_soapFault->getSoapFaultMessage(
                $message,
                Magento_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER,
                'en',
                $details
            )
        );
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Magento_Webapi_Controller_Dispatcher_Soap
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
     * @return Magento_Webapi_Controller_Dispatcher_Soap
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
     * @return Magento_Webapi_Model_Soap_Server
     */
    protected function _initSoapServer()
    {
        $this->_soapServer->initWsdlCache();
        $this->_soapServer->setWSDL($this->_soapServer->generateUri(true))
            ->setEncoding($this->_soapServer->getApiCharset())
            ->setSoapVersion(SOAP_1_2)
            ->setClassmap($this->_apiConfig->getTypeToClassMap());
        use_soap_error_handler(false);
        // TODO: Headers are not available at this point.
        // $this->_soapHandler->setRequestHeaders($this->_getRequestHeaders());
        $this->_soapServer->setReturnResponse(true)->setObject($this->_soapHandler);

        return $this->_soapServer;
    }
}
