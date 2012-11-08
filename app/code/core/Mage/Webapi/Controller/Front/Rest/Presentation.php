<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Controller_Front_Rest_Presentation
{
    /** @var Mage_Webapi_Controller_Front_Rest */
    protected $_frontController;

    /**
     * Renderer
     *
     * @var Mage_Webapi_Controller_Response_RendererInterface
     */
    protected $_renderer;

    function __construct(Mage_Webapi_Controller_Front_Rest $frontController)
    {
        $this->_frontController = $frontController;
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
        $config = $this->_frontController->getApiConfig();
        $apiHelper = $this->_frontController->getHelper();
        $methodReflection = $apiHelper->createMethodReflection($controllerInstance, $action);
        $methodName = $config->getMethodNameWithoutVersionSuffix($methodReflection);
        $bodyParamName = $config->getBodyParamName($methodReflection);
        $requestParams = array_merge(
            $this->getRequest()->getParams(),
            array($bodyParamName => $this->_getRequestBody($methodName))
        );
        /** Convert names of ID and Parent ID params in request to those which are used in method interface. */
        $idParamName = $config->getIdParamName($methodReflection);
        $parentIdParamNameInRoute = Mage_Webapi_Controller_Router_Route_Rest::PARAM_PARENT_ID;
        $idParamNameInRoute = Mage_Webapi_Controller_Router_Route_Rest::PARAM_ID;
        if (isset($requestParams[$parentIdParamNameInRoute]) && ($idParamName != $parentIdParamNameInRoute)) {
            $requestParams[$idParamName] = $requestParams[$parentIdParamNameInRoute];
            unset($requestParams[$parentIdParamNameInRoute]);
        } elseif (isset($requestParams[$idParamNameInRoute]) && ($idParamName != $idParamNameInRoute)) {
            $requestParams[$idParamName] = $requestParams[$idParamNameInRoute];
            unset($requestParams[$idParamNameInRoute]);
        }

        return $apiHelper->prepareMethodParams($controllerInstance, $action, $requestParams, $config);
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
            case 'create':
                // The create action has the dynamic type which depends on data in the request body
                if ($this->getRequest()->isAssocArrayInRequestBody()) {
                    /** @var $createdItem Mage_Core_Model_Abstract */
                    $createdItem = $outputData;
                    $this->getResponse()->setHeader('Location', $this->_getCreatedItemLocation($createdItem));
                } else {
                    // TODO: Consider multiCreate from SOAP (API coverage must be the same for all API types)
                    $this->getResponse()->setHttpResponseCode(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS);
                }
                if ($this->getResponse()->getMessages()) {
                    $this->_render(array('messages' => $this->getResponse()->getMessages()));
                }
                break;
            case 'get':
                // TODO: Implement fields filtration
                $filteredData  = $outputData;
                $this->_render($filteredData);
                break;
            case 'list':
                // TODO: Implement fields filtration
                $filteredData  = $outputData;
                $this->_render($filteredData);
                break;
            case 'multiUpdate':
                $this->_render(array('messages' => $this->getResponse()->getMessages()));
                $this->getResponse()->setHttpResponseCode(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS);
                break;
            case 'multiDelete':
                $this->getResponse()->setHttpResponseCode(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS);
                break;
            case 'update':
                // break intentionally omitted
            case 'delete':
                break;
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
        /* @var $apiTypeRoute Mage_Webapi_Controller_Router_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Webapi_Controller_Router_Route_ApiType');

        $router = new Zend_Controller_Router_Route($this->_frontController->getApiConfig()->getRestRouteToItem(
            $this->getRequest()->getResourceName()));
        $chain = $apiTypeRoute->chain($router);
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            // TODO: ID param can be named differently
            'id' => $createdItem->getId(),
            Mage_Webapi_Controller_Router_Route_Rest::PARAM_VERSION => $this->getRequest()->getResourceVersion()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    // TODO: Temporary proxy
    public function getRequest()
    {
        return $this->_frontController->getRequest();
    }

    // TODO: Temporary proxy
    public function getResponse()
    {
        return $this->_frontController->getResponse();
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
            case 'create':
                $processedInputData = $this->getRequest()->getBodyParams();
                // request data must be checked before the create type identification
                // The create action has the dynamic type which depends on data in the request body
                if ($this->getRequest()->isAssocArrayInRequestBody()) {
                    // TODO: Implement data filtration of item
                } else {
                    // TODO: Implement fields filtration of collection
                }
                break;
            case 'update':
                $processedInputData = $this->getRequest()->getBodyParams();
                // TODO: Implement data filtration
                break;
            case 'multiUpdate':
                $processedInputData = $this->getRequest()->getBodyParams();
                // TODO: Implement fields filtration
                break;
            case 'multiDelete':
                // break intentionally omitted
            case 'get':
                // break intentionally omitted
            case 'delete':
                // break intentionally omitted
            case 'list':
                break;
        }
        return $processedInputData;
    }

    /**
     * Render data using registered Renderer
     *
     * @param mixed $data
     */
    protected function _render($data)
    {
        $mimeType = $this->getRenderer()->getMimeType();
        $body = $this->getRenderer()->render($data);
        $this->getResponse()->setMimeType($mimeType)->setBody($body);
    }

    /**
     * Get renderer if not exists create
     *
     * @return Mage_Webapi_Controller_Response_RendererInterface
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            $renderer = Mage_Webapi_Controller_Response_Renderer::factory($this->getRequest()->getAcceptTypes());
            $this->setRenderer($renderer);
        }

        return $this->_renderer;
    }

    /**
     * Set renderer
     *
     * @param Mage_Webapi_Controller_Response_RendererInterface $renderer
     */
    public function setRenderer(Mage_Webapi_Controller_Response_RendererInterface $renderer)
    {
        $this->_renderer = $renderer;
    }
}
