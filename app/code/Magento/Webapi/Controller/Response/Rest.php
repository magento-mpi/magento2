<?php
/**
 * Web API REST response.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Rest extends Magento_Webapi_Controller_Response
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /** @var Magento_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Magento_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /** @var Magento_Core_Model_App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Magento_Core_Model_App $app
     */
    public function __construct(
        Magento_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Magento_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Magento_Core_Model_App $app
    ) {
        $this->_renderer = $rendererFactory->get();
        $this->_errorProcessor = $errorProcessor;
        $this->_app = $app;
    }

    /**
     * Add exception to the list of exceptions.
     *
     * Replace real error message of untrusted exceptions to prevent potential vulnerability.
     *
     * @param Exception $exception
     * @return Magento_Webapi_Controller_Response_Rest
     */
    public function setException(Exception $exception)
    {
        return parent::setException($this->_errorProcessor->maskException($exception));
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
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            $httpCode = $e->getCode() == Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                ? Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                : Magento_Webapi_Exception::HTTP_INTERNAL_ERROR;

            /** If error was encountered during "error rendering" process then use error renderer. */
            $this->_errorProcessor->renderException($e, $httpCode);
        }
    }

    /**
     * Generate and set HTTP response code, error messages to Response object.
     */
    protected function _renderMessages()
    {
        $formattedMessages = array();
        $formattedMessages['messages'] = $this->getMessages();
        $responseHttpCode = null;
        /** @var Exception $exception */
        foreach ($this->getException() as $exception) {
            $code = ($exception instanceof Magento_Webapi_Exception)
                ? $exception->getCode()
                : Magento_Webapi_Exception::HTTP_INTERNAL_ERROR;
            $messageData = array('code' => $code, 'message' => $exception->getMessage());
            if ($this->_app->isDeveloperMode()) {
                $messageData['trace'] = $exception->getTraceAsString();
            }
            $formattedMessages['messages']['error'][] = $messageData;
            // keep HTTP code for response
            $responseHttpCode = $code;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $this->setHttpResponseCode($responseHttpCode);
        $this->setMimeType($this->_renderer->getMimeType());
        $this->setBody($this->_renderer->render($formattedMessages));
        return $this;
    }
}
