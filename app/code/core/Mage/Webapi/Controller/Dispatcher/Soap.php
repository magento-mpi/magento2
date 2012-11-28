<?php
/**
 * Dispatcher for SOAP API calls.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Dispatcher_Soap implements Mage_Webapi_Controller_DispatcherInterface
{
    /** @var Mage_Webapi_Model_Config_Soap */
    protected $_apiConfig;

    /** @var Mage_Webapi_Model_Soap_Server */
    protected $_soapServer;

    /** @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /** @var Mage_Webapi_Controller_Request_Soap */
    protected $_request;

    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config_Soap $apiConfig
     * @param Mage_Webapi_Controller_Request_Soap $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover
     * @param Mage_Webapi_Model_Soap_Server $soapServer
     * @param Mage_Webapi_Model_Soap_Fault $soapFault
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config_Soap $apiConfig,
        Mage_Webapi_Controller_Request_Soap $request,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Model_Soap_AutoDiscover $autoDiscover,
        Mage_Webapi_Model_Soap_Server $soapServer,
        Mage_Webapi_Model_Soap_Fault $soapFault,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
    ) {
        $this->_helper = $helper;
        $this->_apiConfig = $apiConfig;
        $this->_autoDiscover = $autoDiscover;
        $this->_soapServer = $soapServer;
        $this->_request = $request;
        $this->_soapFault = $soapFault;
        $this->_response = $response;
        $this->_errorProcessor = $errorProcessor;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return Mage_Webapi_Controller_Dispatcher_Soap
     */
    public function dispatch()
    {
        try {
            if ($this->_request->getParam(Mage_Webapi_Model_Soap_Server::REQUEST_PARAM_WSDL) !== null) {
                $this->_setResponseContentType('text/xml');
                $responseBody = $this->_autoDiscover->handle(
                    $this->_request->getRequestedResources(),
                    $this->_soapServer->generateUri()
                );
            } else {
                $this->_setResponseContentType('application/soap+xml');
                $responseBody = $this->_soapServer->handle();
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
        // TODO: handle exceptions from config.
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
                Mage_Webapi_Controller_Dispatcher_Soap_Handler::FAULT_CODE_SENDER,
                'en',
                $details
            )
        );
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return Mage_Webapi_Controller_Dispatcher_Soap
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
     * @return Mage_Webapi_Controller_Dispatcher_Soap
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
