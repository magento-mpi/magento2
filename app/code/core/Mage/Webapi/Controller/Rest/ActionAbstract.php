<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic REST controller
 */
// TODO: Remove inheritance if possible
abstract class Mage_Webapi_Controller_Rest_ActionAbstract
{
    /**#@+
     * Collection page sizes
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX     = 100;
    /**#@-*/

    /**
     * Request
     *
     * @var Mage_Webapi_Model_Request
     */
    protected $_request;

    /**
     * API Version
     *
     * @var int
     */
    protected $_version = null;

    /**
     * Response
     *
     * @var Mage_Webapi_Model_Response
     */
    protected $_response;

    /**
     * Renderer
     *
     * @var Mage_Webapi_Model_Renderer_Interface
     */
    protected $_renderer;

    /**
     * If TRUE - no rendering will be done and dispatch will return data. Otherwise, by default
     *
     * @var bool
     */
    protected $_returnData = false;

    /** @var Mage_Webapi_Helper_Rest */
    protected $_restHelper;

    /**
     * Call action without excessive checks for REST
     *
     * @param $action
     */
    public function dispatch($action)
    {
        $actionMethodName = $this->getActionMethodName($action);
        $this->$actionMethodName();
    }

    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response, array $invokeArgs = array()
    ) {
        $this->_restHelper = Mage::helper('Mage_Webapi_Helper_Rest');
        $this->_request = $request;
        $this->_response = $response;
    }
    /**
     * Set request
     *
     * @param Mage_Webapi_Model_Request $request
     * @return Mage_Webapi_Controller_Rest_ActionAbstract
     */
    public function setRequest(Mage_Webapi_Model_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Determine version from class name
     *
     * @return int
     */
    public function getVersion()
    {
        if (null === $this->_version) {
            if (preg_match('/^.+([1-9]\d*)$/', get_class($this), $matches) ) {
                $this->setVersion($matches[1]);
            } else {
                throw new Exception('Can not determine version from class name');
            }
        }
        return $this->_version;
    }

    /**
     * Set API version
     *
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->_version = (int)$version;
    }

    /**
     * Set response
     *
     * @param Mage_Webapi_Model_Response $response
     */
    public function setResponse(Mage_Webapi_Model_Response $response)
    {
        $this->_response = $response;
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
     * Add non-critical error
     *
     * @param string $message
     * @param int $code
     * @return Mage_Webapi_Controller_Rest_ActionAbstract
     */
    protected function _error($message, $code)
    {
        $this->getResponse()->setException(new Mage_Webapi_Exception($message, $code));
        return $this;
    }

    /**
     * Add success message
     *
     * @param string $message
     * @param int $code
     * @param array $params
     * @return Mage_Webapi_Controller_Rest_ActionAbstract
     */
    protected function _successMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, Mage_Webapi_Model_Response::MESSAGE_TYPE_SUCCESS);
        return $this;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @param int $code
     * @param array $params
     * @return Mage_Webapi_Controller_Rest_ActionAbstract
     */
    protected function _errorMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, Mage_Webapi_Model_Response::MESSAGE_TYPE_ERROR);
        return $this;
    }

    /**
     * Set navigation parameters and apply filters from URL params
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Webapi_Controller_Rest_ActionAbstract
     */
    final protected function _applyCollectionModifiers(Varien_Data_Collection_Db $collection)
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_restHelper->critical(Mage_Webapi_Helper_Rest::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if (null == $pageSize) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_restHelper->critical(Mage_Webapi_Helper_Rest::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            if (!is_string($orderField)
            // TODO: Check if order field is allowed for specified entity
        ) {
                $this->_restHelper->critical(Mage_Webapi_Helper_Rest::RESOURCE_COLLECTION_ORDERING_ERROR);
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);

        return $collection;
    }

    /**
     * Retrieve current store according to request and API user type
     *
     * @return Mage_Core_Model_Store
     */
    protected function _getStore()
    {
        $store = $this->getRequest()->getParam('store');
        if (is_numeric($store)) {
            $store = (int) $store;
        }
        try {
            if (is_null($store)) {
                $store = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
            }
            $store = Mage::app()->getStore($store);
        } catch (Mage_Core_Model_Store_Exception $e) {
            // store does not exist
            $this->_critical('Requested store is invalid', Mage_Webapi_Controller_Front_Rest::HTTP_BAD_REQUEST);
        }
        return $store;
    }

    /**
     * Retrieve action method name
     *
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)
    {
        return $action;
    }

    /**
     * Check if specified action is defined in current controller
     *
     * @param string $actionName
     * @return bool
     */
    public function hasAction($actionName)
    {
        return method_exists($this, $actionName);
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Webapi_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Webapi_Model_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
