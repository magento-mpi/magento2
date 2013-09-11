<?php
/**
 * Dispatcher for SOAP API calls.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher;

class Soap implements \Magento\Webapi\Controller\DispatcherInterface
{
    /** @var \Magento\Webapi\Model\Config\Soap */
    protected $_apiConfig;

    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServer;

    /** @var \Magento\Webapi\Model\Soap\AutoDiscover */
    protected $_autoDiscover;

    /** @var \Magento\Webapi\Controller\Request\Soap */
    protected $_request;

    /** @var \Magento\Webapi\Model\Soap\Fault */
    protected $_soapFault;

    /** @var \Magento\Webapi\Controller\Response */
    protected $_response;

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor */
    protected $_errorProcessor;

    /** @var \Magento\Webapi\Controller\Dispatcher\Soap\Handler */
    protected $_soapHandler;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config\Soap $apiConfig
     * @param \Magento\Webapi\Controller\Request\Soap $request
     * @param \Magento\Webapi\Controller\Response $response
     * @param \Magento\Webapi\Model\Soap\AutoDiscover $autoDiscover
     * @param \Magento\Webapi\Model\Soap\Server $soapServer
     * @param \Magento\Webapi\Model\Soap\Fault $soapFault
     * @param \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
     * @param \Magento\Webapi\Controller\Dispatcher\Soap\Handler $soapHandler
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Soap $apiConfig,
        \Magento\Webapi\Controller\Request\Soap $request,
        \Magento\Webapi\Controller\Response $response,
        \Magento\Webapi\Model\Soap\AutoDiscover $autoDiscover,
        \Magento\Webapi\Model\Soap\Server $soapServer,
        \Magento\Webapi\Model\Soap\Fault $soapFault,
        \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor,
        \Magento\Webapi\Controller\Dispatcher\Soap\Handler $soapHandler
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
     * @return \Magento\Webapi\Controller\Dispatcher\Soap
     */
    public function dispatch()
    {
        try {
            if ($this->_request->getParam(\Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL) !== null) {
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
        } catch (\Exception $e) {
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
                \Magento\Webapi\Model\Soap\Fault::FAULT_CODE_SENDER,
                'en',
                $details
            )
        );
    }

    /**
     * Set content type to response object.
     *
     * @param string $contentType
     * @return \Magento\Webapi\Controller\Dispatcher\Soap
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
     * @return \Magento\Webapi\Controller\Dispatcher\Soap
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
     * @return \Magento\Webapi\Model\Soap\Server
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
