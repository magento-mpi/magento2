<?php

class Mage_Webapi_Controller_Front_Rest_Presentation
{
    /** @var Mage_Webapi_Controller_Front_Rest */
    protected $_frontController;

    /**
     * Renderer
     *
     * @var Mage_Webapi_Model_Renderer_Interface
     */
    protected $_renderer;

    function __construct(Mage_Webapi_Controller_Front_Rest $frontController)
    {
        $this->_frontController = $frontController;
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @param string $methodName
     * @param object $controllerInstance
     * @param string $action
     * @return array
     */
    // TODO: Think about AOP implementation if it is approved by Tech Leads group
    // TODO: Think about interface refactoring
    public function fetchRequestData($methodName, $controllerInstance, $action)
    {
        $methodReflection = new ReflectionMethod($controllerInstance, $action);
        // TODO: Refactor this and take param initialized with post data from anotations
        $parameters = array_merge(
            $this->getRequest()->getParams(),
            array('data' => $this->_getRequestData($methodName))
        );
        $actionArguments = $this->_prepareMethodArguments($methodReflection->getParameters(), $parameters);
        return $actionArguments;
    }

    /**
     * Convert request data into method arguments list.
     * Sort in correct order, set default values for omitted parameters.
     *
     * @param ReflectionParameter[] $reflectionParameters
     * @param array $requestData
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _prepareMethodArguments($reflectionParameters, $requestData) {

        $methodArguments = array();
        foreach($reflectionParameters as $parameter){
            $parameterName = $parameter->getName();
            if( isset( $requestData[$parameterName] ) ){
                $methodArguments[$parameterName] = $requestData[$parameterName];
            } else {
                if($parameter->isOptional()){
                    $methodArguments[$parameterName] = $parameter->getDefaultValue();
                } else {
                    throw new InvalidArgumentException("Required parameter \"$parameterName\" is missing.", 0);
                }
            }
        }
        return $methodArguments;
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
            case 'multiGet':
                // TODO: Implement fields filtration
                $filteredData  = $outputData;
                $this->_render($filteredData);
                break;
                break;
            case 'multiUpdate':
                $this->_render(array('messages' => $this->getResponse()->getMessages()));
                $this->getResponse()->setHttpResponseCode(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS);
                break;
            case 'multiDelete':
                $this->getResponse()->setHttpResponseCode(Mage_Webapi_Controller_Front_Rest::HTTP_MULTI_STATUS);
                break;
            case 'update':
            case 'delete':
                break;
        }
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $createdItem
     * @return string URL
     */
    protected function _getCreatedItemLocation($createdItem)
    {
        /* @var $apiTypeRoute Mage_Webapi_Controller_Router_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Webapi_Controller_Router_Route_ApiType');

        $router = new Zend_Controller_Router_Route($this->_frontController->getRestConfig()->getRouteByResource(
            $this->getRequest()->getResourceName(),
            Mage_Webapi_Controller_Front_Rest::RESOURCE_TYPE_ITEM
        ));
        $chain = $apiTypeRoute->chain($router);
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id'       => $createdItem->getId()
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
     * Retrieve request data. Ensure that data is not empty
     *
     * @return array
     */
    protected function _getRequestData($method)
    {
        $processedInputData = $this->getRequest()->getBodyParams();
        switch ($method) {
            case 'create':
                // request data must be checked before the create type identification
                // The create action has the dynamic type which depends on data in the request body
                if ($this->getRequest()->isAssocArrayInRequestBody()) {
                    // TODO: Implement data filtration of item
                } else {
                    // TODO: Implement fields filtration of collection
                }
                break;
            case 'update':
                // TODO: Implement data filtration
                break;
            case 'multiUpdate':
                // TODO: Implement fields filtration
                break;
            case 'multiDelete':
            case 'get':
            case 'delete':
            case 'multiGet':
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
        $this->getResponse()->setMimeType($this->getRenderer()->getMimeType())
            ->setBody($this->getRenderer()->render($data));
    }

    /**
     * Get renderer if not exists create
     *
     * @return Mage_Webapi_Model_Renderer_Interface
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            $renderer = Mage_Webapi_Model_Renderer::factory($this->getRequest()->getAcceptTypes());
            $this->setRenderer($renderer);
        }

        return $this->_renderer;
    }

    /**
     * Set renderer
     *
     * @param Mage_Webapi_Model_Renderer_Interface $renderer
     */
    public function setRenderer(Mage_Webapi_Model_Renderer_Interface $renderer)
    {
        $this->_renderer = $renderer;
    }
}
