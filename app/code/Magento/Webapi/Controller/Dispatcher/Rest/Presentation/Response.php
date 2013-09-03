<?php
/**
 * Helper for response data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Response
{
    /** @var Magento_Webapi_Model_Config_Rest */
    protected $_apiConfig;

    /** @var Magento_Webapi_Controller_Request_Rest */
    protected $_request;

    /** @var Magento_Webapi_Controller_Response_Rest */
    protected $_response;

    /** @var \Magento\Controller\Router\Route\Factory */
    protected $_routeFactory;

    /** @var Magento_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /** @var Magento_Core_Model_Config */
    protected $_applicationConfig;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Model_Config_Rest $apiConfig
     * @param Magento_Webapi_Controller_Request_Factory $requestFactory
     * @param Magento_Webapi_Controller_Response_Rest $response
     * @param Magento_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param \Magento\Controller\Router\Route\Factory $routeFactory
     * @param Magento_Core_Model_Config $applicationConfig
     */
    public function __construct(
        Magento_Webapi_Model_Config_Rest $apiConfig,
        Magento_Webapi_Controller_Request_Factory $requestFactory,
        Magento_Webapi_Controller_Response_Rest $response,
        Magento_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        \Magento\Controller\Router\Route\Factory $routeFactory,
        Magento_Core_Model_Config $applicationConfig
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
     * @param string $method
     * @param array|null $outputData
     */
    public function prepareResponse($method, $outputData = null)
    {
        switch ($method) {
            case Magento_Webapi_Controller_ActionAbstract::METHOD_CREATE:
                /** @var $createdItem Magento_Core_Model_Abstract */
                $createdItem = $outputData;
                $this->_response->setHeader('Location', $this->_getCreatedItemLocation($createdItem));
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_GET:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_LIST:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE:
                $this->_response->setHttpResponseCode(Magento_Webapi_Controller_Response_Rest::HTTP_MULTI_STATUS);
                break;
            case Magento_Webapi_Controller_ActionAbstract::METHOD_UPDATE:
                // break is intentionally omitted
            case Magento_Webapi_Controller_ActionAbstract::METHOD_DELETE:
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
     * @param Magento_Core_Model_Abstract $createdItem
     * @return string URL
     */
    protected function _getCreatedItemLocation($createdItem)
    {
        $apiTypeRoute = $this->_routeFactory->createRoute(
            'Magento_Webapi_Controller_Router_Route',
            $this->_applicationConfig->getAreaFrontName() . '/:' . Magento_Webapi_Controller_Request::PARAM_API_TYPE
        );
        $resourceName = $this->_request->getResourceName();
        $routeToItem = $this->_routeFactory->createRoute(
            'Zend_Controller_Router_Route',
            $this->_apiConfig->getRestRouteToItem($resourceName)
        );
        $chain = $apiTypeRoute->chain($routeToItem);
        $params = array(
            Magento_Webapi_Controller_Request::PARAM_API_TYPE => $this->_request->getApiType(),
            Magento_Webapi_Controller_Router_Route_Rest::PARAM_ID => $createdItem->getId(),
            Magento_Webapi_Controller_Router_Route_Rest::PARAM_VERSION => $this->_request->getResourceVersion()
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
