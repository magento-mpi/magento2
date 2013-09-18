<?php
/**
 * Front controller for WebAPI SOAP area.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class Soap implements \Magento\Core\Controller\FrontInterface
{
    /**#@+
     * Content types used for responses processed by SOAP web API.
     */
    const CONTENT_TYPE_SOAP_CALL = 'application/soap+xml';
    const CONTENT_TYPE_WSDL_REQUEST = 'text/xml';
    /**#@-*/

    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServer;

    /** @var \Magento\Webapi\Model\Soap\Wsdl\Generator */
    protected $_wsdlGenerator;

    /** @var \Magento\Webapi\Controller\Soap\Request */
    protected $_request;

    /** @var \Magento\Webapi\Controller\Response */
    protected $_response;

    /** @var \Magento\Webapi\Controller\ErrorProcessor */
    protected $_errorProcessor;

    /** @var \Magento\Core\Model\App\State */
    protected $_appState;

    /** @var \Magento\Core\Model\App */
    protected $_application;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Soap\Request $request
     * @param \Magento\Webapi\Controller\Response $response
     * @param \Magento\Webapi\Model\Soap\Wsdl\Generator $wsdlGenerator
     * @param \Magento\Webapi\Model\Soap\Server $soapServer
     * @param \Magento\Webapi\Controller\ErrorProcessor $errorProcessor
     * @param \Magento\Core\Model\App\State $appState
     * @param \Magento\Core\Model\App $application
     */
    public function __construct(
        \Magento\Webapi\Controller\Soap\Request $request,
        \Magento\Webapi\Controller\Response $response,
        \Magento\Webapi\Model\Soap\Wsdl\Generator $wsdlGenerator,
        \Magento\Webapi\Model\Soap\Server $soapServer,
        \Magento\Webapi\Controller\ErrorProcessor $errorProcessor,
        \Magento\Core\Model\App\State $appState,
        \Magento\Core\Model\App $application
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_wsdlGenerator = $wsdlGenerator;
        $this->_soapServer = $soapServer;
        $this->_errorProcessor = $errorProcessor;
        $this->_appState = $appState;
        $this->_application = $application;
    }

    /**
     * Initialize front controller
     *
     * @return \Magento\Webapi\Controller\Soap
     */
    public function init()
    {
        return $this;
    }

    /**
     * Dispatch request to SOAP endpoint.
     *
     * @return \Magento\Webapi\Controller\Soap
     */
    public function dispatch()
    {
        try {
            if (!$this->_appState->isInstalled()) {
                throw new \Magento\Webapi\Exception(__('Magento is not yet installed'));
            }
            if ($this->_isWsdlRequest()) {
                $responseBody = $this->_wsdlGenerator->generate(
                    $this->_request->getRequestedServices(),
                    $this->_soapServer->generateUri()
                );
                $this->_setResponseContentType(self::CONTENT_TYPE_WSDL_REQUEST);
            } else {
                $responseBody = $this->_soapServer->handle();
                $this->_setResponseContentType(self::CONTENT_TYPE_SOAP_CALL);
            }
            $this->_setResponseBody($responseBody);
        } catch (\Exception $e) {
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
        return $this->_request->getParam(\Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL) !== null;
    }

    /**
     * Set body and status code to response using information extracted from provided exception.
     *
     * @param Exception $exception
     */
    protected function _prepareErrorResponse($exception)
    {
        $maskedException = $this->_errorProcessor->maskException($exception);
        $soapFault = new \Magento\Webapi\Model\Soap\Fault($this->_application, $maskedException);
        if ($this->_isWsdlRequest()) {
            $httpCode = $maskedException->getHttpCode();
            $contentType = self::CONTENT_TYPE_WSDL_REQUEST;
        } else {
            $httpCode = \Magento\Webapi\Controller\Response::HTTP_OK;
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
     * @return \Magento\Webapi\Controller\Soap
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
     * @return \Magento\Webapi\Controller\Soap
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
