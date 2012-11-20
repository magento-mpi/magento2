<?php
/**
 * Helper for data processing according to REST presentation.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Handler_Rest_Presentation
{
    /** @var Mage_Webapi_Model_Config */
    protected $_apiConfig;

    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactory;

    /** @var Mage_Webapi_Helper_Data */
    protected $_apiHelper;

    /** @var Mage_Webapi_Controller_Request_Rest */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Controller_Response_Rest_Renderer_Factory */
    protected $_rendererFactory;

    /** @var Magento_Controller_Router_Route_Factory */
    protected $_routeFactory;

    /** @var Mage_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Model_Config $apiConfig
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Webapi_Controller_Request_Factory $requestFactory
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param Magento_Controller_Router_Route_Factory $routeFactory
     */
    public function __construct(
        Mage_Webapi_Model_Config $apiConfig,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Webapi_Controller_Request_Factory $requestFactory,
        Mage_Webapi_Controller_Response $response,
        Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Magento_Controller_Router_Route_Factory $routeFactory
    ) {
        $this->_apiConfig = $apiConfig;
        $this->_helperFactory = $helperFactory;
        $this->_apiHelper = $helperFactory->get('Mage_Webapi_Helper_Data');
        $this->_request = $requestFactory->get();
        $this->_response = $response;
        $this->_rendererFactory = $rendererFactory;
        $this->_routeFactory = $routeFactory;
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @param object $controllerInstance
     * @param string $action
     * @return array
     */
    public function fetchRequestData($controllerInstance, $action)
    {
        $methodReflection = $this->_apiHelper->createMethodReflection($controllerInstance, $action);
        $methodName = $this->_apiConfig->getMethodNameWithoutVersionSuffix($methodReflection);
        $bodyParamName = $this->_apiConfig->getBodyParamName($methodReflection);
        $requestParams = array_merge(
            $this->_request->getParams(),
            array($bodyParamName => $this->_getRequestBody($methodName))
        );
        /** Convert names of ID and Parent ID params in request to those which are used in method interface. */
        $idArgumentName = $this->_apiConfig->getIdParamName($methodReflection);
        $parentIdParamName = Mage_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
        $idParamName = Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID;
        if (isset($requestParams[$parentIdParamName]) && ($idArgumentName != $parentIdParamName)) {
            $requestParams[$idArgumentName] = $requestParams[$parentIdParamName];
            unset($requestParams[$parentIdParamName]);
        } elseif (isset($requestParams[$idParamName]) && ($idArgumentName != $idParamName)) {
            $requestParams[$idArgumentName] = $requestParams[$idParamName];
            unset($requestParams[$idParamName]);
        }

        return $this->_apiHelper->prepareMethodParams($controllerInstance, $action, $requestParams, $this->_apiConfig);
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
            case Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE:
                /** @var $createdItem Mage_Core_Model_Abstract */
                $createdItem = $outputData;
                $this->_response->setHeader('Location', $this->_getCreatedItemLocation($createdItem));
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE:
                $this->_response->setHttpResponseCode(Mage_Webapi_Controller_Handler_Rest::HTTP_MULTI_STATUS);
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_GET:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_LIST:
                // TODO: Implement fields filtration
                $filteredData = $outputData;
                $this->_render($filteredData);
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE:
                $this->_response->setHttpResponseCode(Mage_Webapi_Controller_Handler_Rest::HTTP_MULTI_STATUS);
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE:
                $this->_response->setHttpResponseCode(Mage_Webapi_Controller_Handler_Rest::HTTP_MULTI_STATUS);
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE:
                // break is intentionally omitted
            case Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE:
                break;
        }
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
            'Mage_Webapi_Controller_Router_Route_Webapi',
            Mage_Webapi_Controller_Router_Route_Webapi::getApiRoute()
        );
        $resourceName = $this->_request->getResourceName();
        $routeToItem = $this->_routeFactory->createRoute(
            'Zend_Controller_Router_Route',
            $this->_apiConfig->getRestRouteToItem($resourceName)
        );
        $chain = $apiTypeRoute->chain($routeToItem);
        $params = array(
            Mage_Webapi_Controller_Router_Route_Webapi::PARAM_API_TYPE => $this->_request->getApiType(),
            Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID => $createdItem->getId(),
            Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION => $this->_request->getResourceVersion()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Retrieve request data. Ensure that data is not empty.
     *
     * @param string $method
     * @return array
     */
    protected function _getRequestBody($method)
    {
        $processedInputData = null;
        switch ($method) {
            case Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration of item
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_CREATE:
                $processedInputData = $this->_request->getBodyParams();
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement data filtration
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_UPDATE:
                $processedInputData = $this->_request->getBodyParams();
                // TODO: Implement fields filtration
                break;
            case Mage_Webapi_Controller_ActionAbstract::METHOD_MULTI_DELETE:
                // break is intentionally omitted
            case Mage_Webapi_Controller_ActionAbstract::METHOD_GET:
                // break is intentionally omitted
            case Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE:
                // break is intentionally omitted
            case Mage_Webapi_Controller_ActionAbstract::METHOD_LIST:
                break;
        }
        return $processedInputData;
    }

    /**
     * Render data using registered Renderer.
     *
     * @param mixed $data
     */
    protected function _render($data)
    {
        $mimeType = $this->getRenderer()->getMimeType();
        $body = $this->getRenderer()->render($data);
        $this->_response->setMimeType($mimeType)->setBody($body);
    }

    /**
     * Get renderer or create if it does not exist.
     *
     * @return Mage_Webapi_Controller_Response_Rest_RendererInterface
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            $this->_renderer = $this->_rendererFactory->create($this->_request->getAcceptTypes());
        }
        return $this->_renderer;
    }
}
