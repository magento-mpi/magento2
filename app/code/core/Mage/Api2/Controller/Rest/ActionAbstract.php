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
// TODO: Remove all unnecessary functionality (coppied from Mage_Api2_Model_Resource)
// TODO: Remove inheritance if possible
// TODO: Refactor filtration of attributes
abstract class Mage_Api2_Controller_Rest_ActionAbstract extends Mage_Core_Controller_Varien_Action
{
    /**#@+
     * Common operations for attributes
     */
    const OPERATION_ATTRIBUTE_READ  = 'read';
    const OPERATION_ATTRIBUTE_WRITE = 'write';
    /**#@-*/


    /**#@+
     * Collection page sizes
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX     = 100;
    /**#@-*/

    /**
     * Request
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Resource type
     *
     * @var string
     */
    protected $_resourceType;

    /**
     * Api type
     *
     * @var string
     */
    protected $_apiType;

    /**
     * API Version
     *
     * @var int
     */
    protected $_version = null;

    /**
     * Response
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Attribute Filter
     *
     * @var  Mage_Api2_Model_Acl_Filter
     */
    protected $_filter;

    /**
     * Renderer
     *
     * @var Mage_Api2_Model_Renderer_Interface
     */
    protected $_renderer;

    /**
     * Api user
     *
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_apiUser;

    /**
     * User type
     *
     * @var string
     */
    protected $_userType;

    /**
     * If TRUE - no rendering will be done and dispatch will return data. Otherwise, by default
     *
     * @var bool
     */
    protected $_returnData = false;

    /**
     * @var Mage_Api2_Model_Multicall
     */
    protected $_multicall;

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

    /**
     * Create resource
     */
    public function create()
    {
        // request data must be checked before the create type identification
        $requestData = $this->_getRequestData();
        // The create action has the dynamic type which depends on data in the request body
        if ($this->getRequest()->isAssocArrayInRequestBody()) {
            $filteredData = $this->getFilter()->in($requestData);
            $newItemLocation = $this->_create($filteredData);
            if (is_string($newItemLocation) && !empty($newItemLocation)) {
                $this->getResponse()->setHeader('Location', $newItemLocation);
            }
        } else {
            $filteredData = $this->getFilter()->collectionIn($requestData);
            $this->_multiCreate($filteredData);
            $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_MULTI_STATUS);
        }
        if ($this->getResponse()->getMessages()) {
            $this->_render(array('messages' => $this->getResponse()->getMessages()));
        }
    }

    /**
     * Retrieve resource item
     */
    public function get()
    {
        $retrievedData = $this->_retrieve();
        $filteredData  = $this->getFilter()->out($retrievedData);
        $this->_render($filteredData);
    }

    /**
     * Retrieve resource items list
     */
    public function multiGet()
    {
        $retrievedData = $this->_retrieveCollection();
        $filteredData  = $this->getFilter()->collectionOut($retrievedData);
        $this->_render($filteredData);
    }

    /**
     * Update resource item
     */
    public function update()
    {
        $filteredData = $this->getFilter()->in($this->_getRequestData());
        if (empty($filteredData)) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
        }
        $this->_update($filteredData);
    }

    /**
     * Update resource items list
     */
    public function multiUpdate()
    {

        $filteredData = $this->getFilter()->collectionIn($this->_getRequestData());
        if (empty($filteredData)) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
        }
        $this->_multiUpdate($filteredData);
        $this->_render(array('messages' => $this->getResponse()->getMessages()));
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_MULTI_STATUS);
    }

    /**
     * Delete resource item
     */
    public function delete()
    {
        $this->_delete();
    }

    /**
     * Delete resource items list
     */
    public function multiDelete()
    {
        $this->_multiDelete($this->_getRequestData());
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_MULTI_STATUS);
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
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_REQUEST_DATA_INVALID);
        }
        return $requestData;
    }

    /**
     * Set request
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Resource
     */
    public function setRequest(Mage_Api2_Model_Request $request)
    {
        $this->setResourceType($request->getResourceType());
        $this->setApiType($request->getApiType());
        $this->_request = $request;
        return $this;
    }

    /**
     * Get resource type
     * If not exists get from Request
     *
     * @return string
     */
    public function getResourceType()
    {
        if (!$this->_resourceType) {
            $this->setResourceType($this->getRequest()->getResourceType());
        }
        return $this->_resourceType;
    }

    /**
     * Set resource type
     *
     * @param string $resourceType
     * @return Mage_Api2_Model_Resource
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
        return $this;
    }

    /**
     * Get API type
     * If not exists get from Request.
     *
     * @return string
     */
    public function getApiType()
    {
        if (!$this->_apiType) {
            $this->setApiType($this->getRequest()->getApiType());
        }
        return $this->_apiType;
    }

    /**
     * Set API type
     *
     * @param string $apiType
     * @return Mage_Api2_Model_Resource
     */
    public function setApiType($apiType)
    {
        $this->_apiType = $apiType;
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
     * @param Mage_Api2_Model_Response $response
     */
    public function setResponse(Mage_Api2_Model_Response $response)
    {
        $this->_response = $response;
    }

    /**
     * Get filter if not exists create
     *
     * @return Mage_Api2_Model_Acl_Filter
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            /** @var $filter Mage_Api2_Model_Acl_Filter */
            $filter = Mage::getModel('Mage_Api2_Model_Acl_Filter', $this);
            $this->setFilter($filter);
        }
        return $this->_filter;
    }

    /**
     * Set filter
     *
     * @param Mage_Api2_Model_Acl_Filter $filter
     */
    public function setFilter(Mage_Api2_Model_Acl_Filter $filter)
    {
        $this->_filter = $filter;
    }

    /**
     * Get renderer if not exists create
     *
     * @return Mage_Api2_Model_Renderer_Interface
     */
    public function getRenderer()
    {
        if (!$this->_renderer) {
            $renderer = Mage_Api2_Model_Renderer::factory($this->getRequest()->getAcceptTypes());
            $this->setRenderer($renderer);
        }

        return $this->_renderer;
    }

    /**
     * Set renderer
     *
     * @param Mage_Api2_Model_Renderer_Interface $renderer
     */
    public function setRenderer(Mage_Api2_Model_Renderer_Interface $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * Get user type
     * If not exists get from apiUser
     *
     * @return string
     */
    public function getUserType()
    {
        if (!$this->_userType) {
            $this->setUserType($this->getApiUser()->getType());
        }
        return $this->_userType;
    }

    /**
     * Set user type
     *
     * @param string $userType
     * @return Mage_Api2_Model_Resource
     */
    public function setUserType($userType)
    {
        $this->_userType = $userType;
        return $this;
    }

    /**
     * Get API user
     *
     * @throws Exception
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    public function getApiUser()
    {
        $this->_apiUser = new Mage_Api2_Model_Auth_User_Admin();
        if (!$this->_apiUser) {
            throw new Exception('API user is not set.');
        }
        return $this->_apiUser;
    }

    /**
     * Set API user
     *
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Mage_Api2_Model_Resource
     */
    public function setApiUser(Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $this->_apiUser = $apiUser;
        return $this;
    }

    /**
     * Get API2 config
     *
     * @return Mage_Api2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('Mage_Api2_Model_Config');
    }

    /**
     * Get working model
     *
     * @return Mage_Core_Model_Abstract
     */
    public function getWorkingModel()
    {
        return Mage::getModel($this->getConfig()->getResourceWorkingModel($this->getResourceType()));
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
     * @return Mage_Api2_Model_Resource
     */
    protected function _error($message, $code)
    {
        $this->getResponse()->setException(new Mage_Api2_Exception($message, $code));
        return $this;
    }

    /**
     * Add success message
     *
     * @param string $message
     * @param int $code
     * @param array $params
     * @return Mage_Api2_Model_Resource
     */
    protected function _successMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS);
        return $this;
    }

    /**
     * Fetch validation errors from validator object and set them to rest response
     *
     * @throws Mage_Api2_Exception
     * @param Mage_Api2_Model_Resource_Validator $validator
     */
    protected function _processValidationErrors(Mage_Api2_Model_Resource_Validator $validator)
    {
        $errors = $validator->getErrors();
        $errorsCount = count($errors);
        for ($i = 0; $i < $errorsCount; $i++) {
            if ($i != $errorsCount - 1) {
                $this->_error($errors[$i], Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            } else {
                // the last error must be critical to halt script execution
                Mage::helper('Mage_Api2_Helper_Rest')->critical($errors[$i], Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Add error message
     *
     * @param string $message
     * @param int $code
     * @param array $params
     * @return Mage_Api2_Model_Resource
     */
    protected function _errorMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, Mage_Api2_Model_Response::MESSAGE_TYPE_ERROR);
        return $this;
    }

    /**
     * Set navigation parameters and apply filters from URL params
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource
     */
    final protected function _applyCollectionModifiers(Varien_Data_Collection_Db $collection)
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if (null == $pageSize) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $operation = Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ;
            if (!is_string($orderField)
                || !array_key_exists($orderField, $this->getAvailableAttributes($this->getUserType(), $operation))
            ) {
                Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_ORDERING_ERROR);
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);

        return $this->_applyFilter($collection);
    }

    /**
     * Validate filter data and apply it to collection if possible
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource
     */
    protected function _applyFilter(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter();

        if (!$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            $methodName = 'addAttributeToFilter';
        } elseif (method_exists($collection, 'addFieldToFilter')) {
            $methodName = 'addFieldToFilter';
        } else {
            return $this;
        }
        $allowedAttributes = $this->getFilter()->getAllowedAttributes(self::OPERATION_ATTRIBUTE_READ);

        foreach ($filter as $filterEntry) {
            if (!is_array($filterEntry)
                || !array_key_exists('attribute', $filterEntry)
                || !in_array($filterEntry['attribute'], $allowedAttributes)
            ) {
                Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
            $attributeCode = $filterEntry['attribute'];

            unset($filterEntry['attribute']);

            try {
                $collection->$methodName($attributeCode, $filterEntry);
            } catch(Exception $e) {
                Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
        }
        return $this;
    }

    /**
     * Perform multiple calls to subresources of specified resource
     *
     * @param string $resourceInstanceId
     * @return Mage_Api2_Model_Response
     */
    protected function _multicall($resourceInstanceId)
    {
        if (!$this->_multicall) {
            $this->_multicall = Mage::getModel('Mage_Api2_Model_Multicall');
        }
        $resourceName = $this->getResourceType();
        return $this->_multicall->call($resourceInstanceId, $resourceName, $this->getRequest());
    }

    /**
     * Create model of specified resource and configure it with current object attributes
     *
     * @param string $resourceId Resource identifier
     * @param array $requestParams Parameters to be set to request
     * @return Mage_Api2_Model_Resource
     */
    protected function _getSubModel($resourceId, array $requestParams)
    {
        $resourceModel = Mage_Api2_Model_Dispatcher::loadResourceModel(
            $this->getConfig()->getResourceModel($resourceId),
            $this->getApiType(),
            $this->getUserType(),
            $this->getVersion()
        );

        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getModel('Mage_Api2_Model_Request');

        $request->setParams($requestParams);

        $resourceModel
            ->setRequest($request) // request MUST be set first
            ->setApiUser($this->getApiUser())
            ->setApiType($this->getApiType())
            ->setResourceType($resourceId)
            ->setOperation($this->getOperation())
            ->setReturnData(true);

        return $resourceModel;
    }

    /**
     * Check ACL permission for specified resource with current other conditions
     *
     * @param string $resourceId Resource identifier
     * @return bool
     * @throws Exception
     */
    protected function _isSubCallAllowed($resourceId)
    {
        /** @var $globalAcl Mage_Api2_Model_Acl_Global */
        $globalAcl = Mage::getSingleton('Mage_Api2_Model_Acl_Global');

        try {
            return $globalAcl->isAllowed($this->getApiUser(), $resourceId, $this->getOperation());
        } catch (Mage_Api2_Exception $e) {
            throw new Exception('Invalid arguments for isAllowed() call');
        }
    }

    /**
     * Set 'returnData' flag
     *
     * @param boolean $flag
     * @return Mage_Api2_Model_Resource
     */
    public function setReturnData($flag)
    {
        $this->_returnData = $flag;
        return $this;
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation($resource)
    {
        /* @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithEntityTypeAction($this->getResourceType()))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id'       => $resource->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Resource specific method to retrieve attributes' codes. May be overriden in child.
     *
     * @return array
     */
    protected function _getResourceAttributes()
    {
        return array();
    }

    /**
     * Get available attributes of API resource
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getAvailableAttributes($userType, $operation)
    {
        $available     = $this->getAvailableAttributesFromConfig();
        $excludedAttrs = $this->getExcludedAttributes($userType, $operation);
        $includedAttrs = $this->getIncludedAttributes($userType, $operation);
        $entityOnlyAttrs = $this->getEntityOnlyAttributes($userType, $operation);
        $resourceAttrs = $this->_getResourceAttributes();

        // if resource returns not-associative array - attributes' codes only
        if (0 === key($resourceAttrs)) {
            $resourceAttrs = array_combine($resourceAttrs, $resourceAttrs);
        }
        foreach ($resourceAttrs as $attrCode => $attrLabel) {
            if (!isset($available[$attrCode])) {
                $available[$attrCode] = empty($attrLabel) ? $attrCode : $attrLabel;
            }
        }
        foreach (array_keys($available) as $code) {
            if (in_array($code, $excludedAttrs) || ($includedAttrs && !in_array($code, $includedAttrs))) {
                unset($available[$code]);
            }
            if (in_array($code, $entityOnlyAttrs)) {
                $available[$code] .= ' *';
            }
        }
        return $available;
    }

    /**
     * Get excluded attributes for user type
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getExcludedAttributes($userType, $operation)
    {
        return $this->getConfig()->getResourceExcludedAttributes($this->getResourceType(), $userType, $operation);
    }

    /**
     * Get forced attributes
     *
     * @return array
     */
    public function getForcedAttributes()
    {
        return $this->getConfig()->getResourceForcedAttributes($this->getResourceType(), $this->getUserType());
    }

    /**
     * Retrieve list of included attributes
     *
     * @param string $userType API user type
     * @param string $operationType Type of operation: one of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return array
     */
    public function getIncludedAttributes($userType, $operationType)
    {
        return $this->getConfig()->getResourceIncludedAttributes($this->getResourceType(), $userType, $operationType);
    }

    /**
     * Retrieve list of entity only attributes
     *
     * @param string $userType API user type
     * @param string $operationType Type of operation: one of Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_... constant
     * @return array
     */
    public function getEntityOnlyAttributes($userType, $operationType)
    {
        return $this->getConfig()->getResourceEntityOnlyAttributes($this->getResourceType(), $userType, $operationType);
    }

    /**
     * Get available attributes of API resource from configuration file
     *
     * @return array
     */
    public function getAvailableAttributesFromConfig()
    {
        return $this->getConfig()->getResourceAttributes($this->getResourceType());
    }

    /**
     * Get available attributes of API resource from data base
     *
     * @return array
     */
    public function getDbAttributes()
    {
        $available = array();
        $workModel = $this->getConfig()->getResourceWorkingModel($this->getResourceType());

        if ($workModel) {
            /* @var $resource Mage_Core_Model_Resource_Db_Abstract */
            $resource = Mage::getResourceModel($workModel);

            if (method_exists($resource, 'getMainTable')) {
                $available = array_keys($resource->getReadConnection()->describeTable($resource->getMainTable()));
            }
        }
        return $available;
    }

    /**
     * Get EAV attributes of working model
     *
     * @param bool $onlyVisible OPTIONAL Show only the attributes which are visible on frontend
     * @param bool $excludeSystem OPTIONAL Exclude attributes marked as system
     * @return array
     */
    public function getEavAttributes($onlyVisible = false, $excludeSystem = false)
    {
        $attributes = array();
        $model = $this->getConfig()->getResourceWorkingModel($this->getResourceType());

        /** @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('Mage_Eav_Model_Entity_Type')->load($model, 'entity_model');

        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach ($entityType->getAttributeCollection() as $attribute) {
            if ($onlyVisible && !$attribute->getIsVisible()) {
                continue;
            }
            if ($excludeSystem && $attribute->getIsSystem()) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        return $attributes;
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
            if ($this->getUserType() != Mage_Api2_Model_Auth_User_Admin::USER_TYPE) {
                // customer or guest role
                if (!$store) {
                    $store = Mage::app()->getDefaultStoreView();
                } else {
                    $store = Mage::app()->getStore($store);
                }
                if (!$store->getIsActive()) {
                    Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_NOT_FOUND);
                }
            } else {
                // admin role
                if (is_null($store)) {
                    $store = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
                }
                $store = Mage::app()->getStore($store);
            }
        } catch (Mage_Core_Model_Store_Exception $e) {
            // store does not exist
            $this->_critical('Requested store is invalid', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        return $store;
    }

    /**
     * Create resource
     *
     * @param array $filteredData
     * @return string Created resource location
     */
    protected function _create(array $filteredData)
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Create a list of resources
     *
     * @param array $filteredData
     */
    protected function _multiCreate(array $filteredData)
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Retrieve resource data
     *
     * @return array
     */
    protected function _retrieve()
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Retrieve a list of resources
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Update resource
     *
     * @param array $filteredData
     */
    protected function _update(array $filteredData)
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Update a list of resources
     *
     * @param array $filteredData
     */
    protected function _multiUpdate(array $filteredData)
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Delete resource
     */
    protected function _delete()
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Delete a list of resources
     *
     * @param array $filteredData
     */
    protected function _multiDelete(array $filteredData)
    {
        Mage::helper('Mage_Api2_Helper_Rest')->critical(Mage_Api2_Helper_Rest::RESOURCE_METHOD_NOT_IMPLEMENTED);
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
}
