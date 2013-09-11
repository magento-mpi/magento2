<?php
/**
 * Web API REST response.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Response;

class Rest extends \Magento\Webapi\Controller\Response
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /** @var \Magento\Webapi\Controller\Dispatcher\ErrorProcessor */
    protected $_errorProcessor;

    /** @var \Magento\Webapi\Controller\Response\Rest\RendererInterface */
    protected $_renderer;

    /** @var \Magento\Core\Model\App */
    protected $_app;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Response\Rest\Renderer\Factory $rendererFactory
     * @param \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor
     * @param \Magento\Core\Model\App $app
     */
    public function __construct(
        \Magento\Webapi\Controller\Response\Rest\Renderer\Factory $rendererFactory,
        \Magento\Webapi\Controller\Dispatcher\ErrorProcessor $errorProcessor,
        \Magento\Core\Model\App $app
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
     * @param \Exception $exception
     * @return \Magento\Webapi\Controller\Response\Rest
     */
    public function setException(\Exception $exception)
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
        } catch (\Exception $e) {
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            $httpCode = $e->getCode() == \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE
                ? \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE
                : \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR;

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
        /** @var \Exception $exception */
        foreach ($this->getException() as $exception) {
            $code = ($exception instanceof \Magento\Webapi\Exception)
                ? $exception->getCode()
                : \Magento\Webapi\Exception::HTTP_INTERNAL_ERROR;
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
