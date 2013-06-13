<?php
/**
 * Helper for response data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Response
{
    /** @var Mage_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response_Rest */
    protected $_response;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /** @var Mage_Core_Model_Config */
    protected $_applicationConfig;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Config_Rest $apiConfig
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     * @param Mage_Webapi_Controller_Response_Rest $response
     * @param Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     * @param Mage_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Mage_Webapi_Model_Config_Rest $apiConfig,
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Response_Rest $response,
        Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Magento_Controller_Router_Route_Factory $routeFactory,
        Mage_Core_Model_Config $applicationConfig
    ) {
        $this->_apiConfig = $apiConfig;
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
        switch (strtoupper($this->_request->getHttpMethod())) {
            // TODO: Introduce constants instead of literals
            case 'POST':
                /** @var $createdItem Mage_Core_Model_Abstract */
                // TODO: Refactor.Currently uses legacy Mage_Webapi_Model_Config_Rest
                //$this->_response->setHeader('Location', $this->_getCreatedItemLocation($outputData));
                break;
            case 'GET':
                $this->_render($outputData);
                break;
            case 'PUT':
                // break is intentionally omitted
            case 'DELETE':
                break;
        }
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
     * Generate resource location.
     *
     * @param Mage_Core_Model_Abstract $createdItem
     * @return string URL
     */
    protected function _getCreatedItemLocation($createdItem)
    {
        $apiTypeRoute = $this->_routeFactory->createRoute(
            'Mage_Webapi_Controller_Router_Route',
            $this->_applicationConfig->getAreaFrontName() . '/:' . Mage_Webapi_Controller_Request::PARAM_API_TYPE
        );
        $resourceName = $this->_request->getResourceName();
        $routeToItem = $this->_routeFactory->createRoute(
            'Zend_Controller_Router_Route',
            $this->_apiConfig->getRestRouteToItem($resourceName)
        );
        $chain = $apiTypeRoute->chain($routeToItem);
        $params = array(
            Mage_Webapi_Controller_Request::PARAM_API_TYPE => $this->_request->getApiType(),
            Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID => $createdItem->getId(),
            Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION => $this->_request->getResourceVersion()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
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
