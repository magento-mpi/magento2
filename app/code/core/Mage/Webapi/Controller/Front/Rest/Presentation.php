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

    // TODO: Think about AOP implementation if it is approved by Tech Leads group
    public function fetchRequestData($method)
    {
        $processedInputData = null;
        switch ($method) {
            case 'create':
                // request data must be checked before the create type identification
                $requestData = $this->_getRequestData();
                // The create action has the dynamic type which depends on data in the request body
                if ($this->getRequest()->isAssocArrayInRequestBody()) {
                    // TODO: Implement data filtration of item
                    $processedInputData = $requestData;
                } else {
                    // TODO: Implement fields filtration of collection
                    $processedInputData = $requestData;
                }
                break;
            case 'update':
                // TODO: Implement data filtration
                $processedInputData = $this->_getRequestData();
                if (empty($processedInputData)) {
                    Mage::helper('Mage_Webapi_Helper_Rest')->critical(Mage_Webapi_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
                }
                break;
            case 'multiUpdate':
                // TODO: Implement fields filtration
                $processedInputData = $this->_getRequestData();
                if (empty($processedInputData)) {
                    Mage::helper('Mage_Webapi_Helper_Rest')->critical(Mage_Webapi_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
                }
                break;
            case 'multiDelete':
                $processedInputData = $this->_getRequestData();
                break;
            case 'get':
                $processedInputData = array('id' => $this->getRequest()->getParam('id'));
                break;
            case 'multiGet':
            case 'delete':
                break;

        }
        return $processedInputData;
    }

    /**
     * @param $method
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
    protected function _getRequestData()
    {
        $requestData = $this->getRequest()->getBodyParams();
        if (empty($requestData)) {
            Mage::helper('Mage_Webapi_Helper_Rest')->critical(Mage_Webapi_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
        }
        return $requestData;
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
