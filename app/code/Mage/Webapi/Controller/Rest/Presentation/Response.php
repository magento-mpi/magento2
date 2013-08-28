<?php
/**
 * Helper for response data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest_Presentation_Response
{
    /** @var Mage_Webapi_Controller_Rest_Request */
    protected $_request;

    /** @var Mage_Webapi_Controller_Rest_Response */
    protected $_response;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Webapi_Controller_Rest_Response_RendererInterface */
    protected $_renderer;

    /** @var Mage_Core_Model_Config */
    protected $_applicationConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     * @param Mage_Webapi_Controller_Rest_Response $response
     * @param Mage_Webapi_Controller_Rest_Response_Renderer_Factory $rendererFactory
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     * @param Mage_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Rest_Response $response,
        Mage_Webapi_Controller_Rest_Response_Renderer_Factory $rendererFactory,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Core_Model_Config $applicationConfig
    ) {
        $this->_request = $requestFactory->get();
        $this->_response = $response;
        $this->_routeFactory = $routeFactory;
        $this->_renderer = $rendererFactory->get();
        $this->_applicationConfig = $applicationConfig;
    }

    /**
     * Perform rendering of action results.
     *
     * @param array|null $outputData
     */
    public function prepareResponse($outputData = null)
    {
        $this->_render($outputData);
        $this->_renderMessages();
    }

    /**
     * Render error and success messages.
     */
    protected function _renderMessages()
    {
        if ($this->_response->getMessages()) {
            $this->_render(array('messages' => $this->_response->getMessages()));
        }
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
        $this->_response->setMimeType($mimeType)->setBody($body);
    }
}
