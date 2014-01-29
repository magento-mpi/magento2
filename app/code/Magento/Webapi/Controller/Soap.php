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

use Magento\Service\AuthorizationException;
use Magento\Webapi\Exception as WebapiException;

/**
 * TODO: Consider warnings suppression removal
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Soap implements \Magento\App\FrontControllerInterface
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

    /** @var \Magento\App\State */
    protected $_appState;

    /** @var \Magento\Core\Model\App */
    protected $_application;

    /** @var \Magento\Oauth\OauthInterface */
    protected $_oauthService;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Soap\Request $request
     * @param \Magento\Webapi\Controller\Response $response
     * @param \Magento\Webapi\Model\Soap\Wsdl\Generator $wsdlGenerator
     * @param \Magento\Webapi\Model\Soap\Server $soapServer
     * @param \Magento\Webapi\Controller\ErrorProcessor $errorProcessor
     * @param \Magento\App\State $appState
     * @param \Magento\AppInterface $application
     * @param \Magento\Oauth\OauthInterface $oauthService
     */
    public function __construct(
        \Magento\Webapi\Controller\Soap\Request $request,
        \Magento\Webapi\Controller\Response $response,
        \Magento\Webapi\Model\Soap\Wsdl\Generator $wsdlGenerator,
        \Magento\Webapi\Model\Soap\Server $soapServer,
        \Magento\Webapi\Controller\ErrorProcessor $errorProcessor,
        \Magento\App\State $appState,
        \Magento\AppInterface $application,
        \Magento\Oauth\OauthInterface $oauthService
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_wsdlGenerator = $wsdlGenerator;
        $this->_soapServer = $soapServer;
        $this->_errorProcessor = $errorProcessor;
        $this->_appState = $appState;
        $this->_application = $application;
        $this->_oauthService = $oauthService;
    }

    /**
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\App\ResponseInterface
     */
    public function dispatch(\Magento\App\RequestInterface $request)
    {
        $pathParts = explode('/', trim($request->getPathInfo(), '/'));
        array_shift($pathParts);
        $request->setPathInfo('/' . implode('/', $pathParts));
        try {
            if (!$this->_appState->isInstalled()) {
                throw new WebapiException(__('Magento is not yet installed'));
            }
            if ($this->_isWsdlRequest()) {
                $responseBody = $this->_wsdlGenerator->generate(
                    $this->_request->getRequestedServices(),
                    $this->_soapServer->generateUri()
                );
                $this->_setResponseContentType(self::CONTENT_TYPE_WSDL_REQUEST);
                $this->_setResponseBody($responseBody);
            } else {
                $consumerId = $this->_oauthService->validateAccessToken($this->_getAccessToken());
                $this->_request->setConsumerId($consumerId);
                $this->_soapServer->handle();
            }
        } catch (\Exception $e) {
            $this->_prepareErrorResponse($e);
        }
        return $this->_response;
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
     * Parse the Authorization header and return the access token e.g. Authorization: Bearer <access-token>
     *
     * @return string Access token
     * @throws AuthorizationException
     */
    protected function _getAccessToken()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = explode(' ', $_SERVER['HTTP_AUTHORIZATION']);
            if (isset($token[1]) && is_string($token[1])) {
                return $token[1];
            }
            throw new AuthorizationException(__('Authentication header format is invalid.'));
        }
        throw new AuthorizationException(__('Authentication header is absent.'));
    }

    /**
     * Set body and status code to response using information extracted from provided exception.
     *
     * @param \Exception $exception
     */
    protected function _prepareErrorResponse($exception)
    {
        $maskedException = $this->_errorProcessor->maskException($exception);
        if ($this->_isWsdlRequest()) {
            $httpCode = $maskedException->getHttpCode();
            $contentType = self::CONTENT_TYPE_WSDL_REQUEST;
        } else {
            $httpCode = \Magento\Webapi\Controller\Response::HTTP_OK;
            $contentType = self::CONTENT_TYPE_SOAP_CALL;
        }
        $this->_setResponseContentType($contentType);
        $this->_response->setHttpResponseCode($httpCode);
        $soapFault = new \Magento\Webapi\Model\Soap\Fault($this->_application, $this->_soapServer, $maskedException);
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
