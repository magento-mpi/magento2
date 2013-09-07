<?php
/**
 * Web API REST response.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Response extends Magento_Webapi_Controller_Response
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /** @var Magento_Webapi_Controller_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Webapi_Controller_Rest_Response_RendererInterface */
    protected $_renderer;

    /** @var Magento_Core_Model_App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Rest_Response_Renderer_Factory $rendererFactory
     * @param Magento_Webapi_Controller_ErrorProcessor $errorProcessor
     * @param Magento_Core_Model_App $app
     */
    public function __construct(
        Magento_Webapi_Controller_Rest_Response_Renderer_Factory $rendererFactory,
        Magento_Webapi_Controller_ErrorProcessor $errorProcessor,
        Magento_Core_Model_App $app
    ) {
        $this->_renderer = $rendererFactory->get();
        $this->_errorProcessor = $errorProcessor;
        $this->_app = $app;
    }

    /**
     * Send response to the client, render exceptions if they are present.
     */
    public function sendResponse()
    {
        try {
            if ($this->isException()) {
                $this->_renderMessages();
            }
            parent::sendResponse();
        } catch (Exception $e) {
            if ($e instanceof Magento_Webapi_Exception) {
                // If the server does not support all MIME types accepted by the client it SHOULD send 406.
                $httpCode = $e->getHttpCode() == Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                    ? Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                    : Magento_Webapi_Exception::HTTP_INTERNAL_ERROR;
            } else {
                $httpCode = Magento_Webapi_Exception::HTTP_INTERNAL_ERROR;
            }

            /** If error was encountered during "error rendering" process then use error renderer. */
            $this->_errorProcessor->renderException($e, $httpCode);
        }
    }

    /**
     * Generate and set HTTP response code, error messages to Response object.
     */
    protected function _renderMessages()
    {
        $formattedMessages = $this->getMessages();
        $responseHttpCode = null;
        /** @var Exception $exception */
        foreach ($this->getException() as $exception) {
            // TODO: Move the following checks to error processor
            if ($exception instanceof Magento_Service_ResourceNotFoundException) {
                $httpCode = Magento_Webapi_Exception::HTTP_NOT_FOUND;
            } elseif ($exception instanceof Magento_Service_AuthorizationException) {
                $httpCode = Magento_Webapi_Exception::HTTP_UNAUTHORIZED;
            } elseif ($exception instanceof Magento_Service_Exception) {
                $httpCode = Magento_Webapi_Exception::HTTP_BAD_REQUEST;
            } elseif ($exception instanceof Magento_Webapi_Exception) {
                $httpCode = $exception->getHttpCode();
            } else {
                $httpCode = Magento_Webapi_Exception::HTTP_INTERNAL_ERROR;
            }

            $messageData = array('code' => $exception->getCode(), 'message' => $exception->getMessage());
            if ($exception instanceof Magento_Service_Exception) {
                /** @var Magento_Service_Exception $exception */
                $messageData['parameters'] = $exception->getParameters();
            } elseif ($exception instanceof Magento_Webapi_Exception) {
                $messageData['http_code'] = $exception->getHttpCode();
            }
            if ($this->_app->isDeveloperMode()) {
                $messageData['trace'] = $exception->getTraceAsString();
            }
            $formattedMessages['errors'][] = $messageData;
            // keep HTTP code for response
            $responseHttpCode = $httpCode;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $this->setHttpResponseCode($responseHttpCode);
        $this->setMimeType($this->_renderer->getMimeType());
        $this->setBody($this->_renderer->render($formattedMessages));
        return $this;
    }

    /**
     * Perform rendering of response data.
     *
     * @param array|null $outputData
     * @return Magento_Webapi_Controller_Rest_Response
     */
    public function prepareResponse($outputData = null)
    {
        $this->_render($outputData);
        if ($this->getMessages()) {
            $this->_render(array('messages' => $this->getMessages()));
        };
        return $this;
    }

    /**
     * Render data using registered Renderer.
     *
     * @param mixed $data
     */
    protected function _render($data)
    {
        $mimeType = $this->_renderer->getMimeType();
        $body = $this->_renderer->render($data);
        $this->setMimeType($mimeType)->setBody($body);
    }
}
