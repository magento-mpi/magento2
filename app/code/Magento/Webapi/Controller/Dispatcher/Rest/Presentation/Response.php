<?php
/**
 * Helper for response data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher\Rest\Presentation;

class Response
{
    /** @var \Magento\Webapi\Model\Config\Rest */
    protected $_apiConfig;

    /** @var \Magento\Webapi\Controller\Request\Rest */
    protected $_request;

    /** @var \Magento\Webapi\Controller\Response\Rest */
    protected $_response;

    /** @var \Magento\Controller\Router\Route\Factory */
    protected $_routeFactory;

    /** @var \Magento\Webapi\Controller\Response\Rest\RendererInterface */
    protected $_renderer;

    /** @var \Magento\Core\Model\Config */
    protected $_applicationConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Model\Config\Rest $apiConfig
     * @param \Magento\Webapi\Controller\Request\Factory $requestFactory
     * @param \Magento\Webapi\Controller\Response\Rest $response
     * @param \Magento\Webapi\Controller\Response\Rest\Renderer\Factory $rendererFactory
     * @param \Magento\Controller\Router\Route\Factory $routeFactory
     * @param \Magento\Core\Model\Config $applicationConfig
     */
    public function __construct(
        \Magento\Webapi\Model\Config\Rest $apiConfig,
        \Magento\Webapi\Controller\Request\Factory $requestFactory,
        \Magento\Webapi\Controller\Response\Rest $response,
        \Magento\Webapi\Controller\Response\Rest\Renderer\Factory $rendererFactory,
        \Magento\Controller\Router\Route\Factory $routeFactory,
        \Magento\Core\Model\Config $applicationConfig
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
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_CREATE:
                /** @var $createdItem \Magento\Core\Model\AbstractModel */
                $createdItem = $outputData;
                $this->_response->setHeader('Location', $this->_getCreatedItemLocation($createdItem));
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_GET:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_LIST:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_UPDATE:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_CREATE:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_MULTI_DELETE:
                $this->_response->setHttpResponseCode(\Magento\Webapi\Controller\Response\Rest::HTTP_MULTI_STATUS);
                break;
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_UPDATE:
                // break is intentionally omitted
            case \Magento\Webapi\Controller\ActionAbstract::METHOD_DELETE:
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
     * @param \Magento\Core\Model\AbstractModel $createdItem
     * @return string URL
     */
    protected function _getCreatedItemLocation($createdItem)
    {
        $apiTypeRoute = $this->_routeFactory->createRoute(
            'Magento\Webapi\Controller\Router\Route',
            $this->_applicationConfig->getAreaFrontName() . '/:' . \Magento\Webapi\Controller\Request::PARAM_API_TYPE
        );
        $resourceName = $this->_request->getResourceName();
        $routeToItem = $this->_routeFactory->createRoute(
            'Zend_Controller_Router_Route',
            $this->_apiConfig->getRestRouteToItem($resourceName)
        );
        $chain = $apiTypeRoute->chain($routeToItem);
        $params = array(
            \Magento\Webapi\Controller\Request::PARAM_API_TYPE => $this->_request->getApiType(),
            \Magento\Webapi\Controller\Router\Route\Rest::PARAM_ID => $createdItem->getId(),
            \Magento\Webapi\Controller\Router\Route\Rest::PARAM_VERSION => $this->_request->getResourceVersion()
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
