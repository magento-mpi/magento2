<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Abstract Resource
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Resource
{
    /**#@+
     *  Action types
     */
    const ACTION_TYPE_ENTITY = 'entity';
    const ACTION_TYPE_COLLECTION  = 'collection';
    /**#@-*/

    /**#@+
     * Operations. Resource method names
     */
    const OPERATION_CREATE   = 'create';
    const OPERATION_RETRIEVE = 'retrieve';
    const OPERATION_UPDATE   = 'update';
    const OPERATION_DELETE   = 'delete';
    /**#@-*/

    /**#@+
     * Common operations for attributes
     */
    const OPERATION_ATTRIBUTE_READ  = 'read';
    const OPERATION_ATTRIBUTE_WRITE = 'write';
    /**#@-*/

    /**#@+
     *  Default error messages
     */
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.';
    const RESOURCE_DATA_INVALID = 'Resource data invalid.'; //error while checking data inside method
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    const RESOURCE_REQUEST_DATA_INVALID = 'The request data is invalid.';
    /**#@-*/

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR     = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_ORDERING_ERROR   = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR  = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**#@+
     *  Default success messages
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource updated successful.';
    /**#@-*/

    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 10;

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
     * One of Mage_Api2_Model_Resource::ACTION_TYPE_... constant
     *
     * @var string
     */
    protected $_actionType;

    /**
     * One of Mage_Api2_Model_Resource::OPERATION_... constant
     *
     * @var string
     */
    protected $_operation;

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
     * Resource model dispatch
     */
    final public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Create */
            case self::ACTION_TYPE_ENTITY . self::OPERATION_CREATE:
                // Creation of objects is possible only when working with collection
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_CREATE:
                $requestData = $this->getRequest()->getBodyParams();
                if (empty($requestData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $filteredData = $this->getFilter()->in($requestData);
                // The create action has the dynamic type which depends on data in the request body
                if ($this->getRequest()->isAssocArrayInRequestBody()) {
                    if (empty($filteredData)) {
                        $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                    }
                    $newItemLocation = $this->_create($filteredData);
                    $this->getResponse()->setHeader('Location', $newItemLocation);
                } else {
                    $this->_multiCreate($filteredData);
                    $this->_render($this->getResponse()->getMessages());
                }
                break;
            /* Retrieve */
            case self::ACTION_TYPE_ENTITY . self::OPERATION_RETRIEVE:
                $retrievedData = $this->_retrieve();
                $filteredData  = $this->getFilter()->out($retrievedData);
                $this->_render($filteredData);
                break;
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_RETRIEVE:
                $retrievedData = $this->_retrieveCollection();
                $filteredData  = $this->getFilter()->collectionOut($retrievedData);
                $this->_render($filteredData);
                break;
            /* Update */
            case self::ACTION_TYPE_ENTITY . self::OPERATION_UPDATE:
                $requestData = $this->getRequest()->getBodyParams();
                if (empty($requestData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $filteredData = $this->getFilter()->in($requestData);
                if (empty($filteredData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $this->_update($filteredData);
                break;
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_UPDATE:
                $requestData = $this->getRequest()->getBodyParams();
                if (empty($requestData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $filteredData = $this->getFilter()->collectionIn($requestData);
                $this->_multiUpdate($filteredData);
                $this->_render($this->getResponse()->getMessages());
                break;
            /* Delete */
            case self::ACTION_TYPE_ENTITY . self::OPERATION_DELETE:
                $this->_delete();
                break;
            case self::ACTION_TYPE_COLLECTION . self::OPERATION_DELETE:
                $requestData = $this->getRequest()->getBodyParams();
                if (empty($requestData)) {
                    $this->_critical(self::RESOURCE_REQUEST_DATA_INVALID);
                }
                $this->_multiDelete($requestData);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function _multiCreate(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @return array
     */
    protected function _retrieve()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function _multiUpdate(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function _multiDelete(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Get request
     *
     * @throws Exception
     * @return Mage_Api2_Model_Request
     */
    public function getRequest()
    {
        if (!$this->_request) {
            throw new Exception('Request is not set.');
        }
        return $this->_request;
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
     * Get response
     *
     * @return Mage_Api2_Model_Response
     */
    public function getResponse()
    {
        if (!$this->_response) {
            throw new Exception('Response is not set.');
        }
        return $this->_response;
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
            $filter = Mage::getModel('api2/acl_filter', $this);

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
     * Get action type
     * If not exists get from Request
     *
     * @return string One of Mage_Api2_Model_Resource::ACTION_TYPE_... constant
     */
    public function getActionType()
    {
        if (!$this->_actionType) {
            $this->setActionType($this->getRequest()->getActionType());
        }
        return $this->_actionType;
    }

    /**
     * Set route type
     *
     * @param string $actionType One of Mage_Api2_Model_Resource::ACTION_TYPE_... constant
     * @return Mage_Api2_Model_Resource
     */
    public function setActionType($actionType)
    {
        $this->_actionType = $actionType;
        return $this;
    }

    /**
     * Get operation
     * If not exists get from Request
     *
     * @return string One of Mage_Api2_Model_Resource::OPERATION_... constant
     */
    public function getOperation()
    {
        if (!$this->_operation) {
            $this->setOperation($this->getRequest()->getOperation());
        }
        return $this->_operation;
    }

    /**
     * Set operation
     *
     * @param string $operation One of Mage_Api2_Model_Resource::OPERATION_... constant
     * @return Mage_Api2_Model_Resource
     */
    public function setOperation($operation)
    {
        $this->_operation = $operation;
        return $this;
    }

    /**
     * Get API2 config
     *
     * @return Mage_Api2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('api2/config');
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
     * Retrieve name of object id field
     *
     * @return string
     */
    public function getIdFieldName()
    {
        return $this->getConfig()->getResourceIdFieldName($this->getResourceType());
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
     * Throw exception, critical error - stop execution
     *
     * @param string $message
     * @param int $code
     * @throws Mage_Api2_Exception
     */
    protected function _critical($message, $code = null)
    {
        if ($code === null) {
            $errors = $this->_getCriticalErrors();
            if (!isset($errors[$message])) {
                throw new Exception(
                    sprintf('Invalid error "%s" or error code missed.', $message),
                    Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
                );
            }
            $code = $errors[$message];
        }
        throw new Mage_Api2_Exception($message, $code);
    }

    /**
     * Retrieve array with critical errors mapped to HTTP codes
     *
     * @return array
     */
    protected function _getCriticalErrors()
    {
        return array(
            '' => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_NOT_FOUND => Mage_Api2_Model_Server::HTTP_NOT_FOUND,
            self::RESOURCE_METHOD_NOT_ALLOWED => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_METHOD_NOT_IMPLEMENTED => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_DATA_PRE_VALIDATION_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_INTERNAL_ERROR => Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
            self::RESOURCE_UNKNOWN_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_REQUEST_DATA_INVALID => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_COLLECTION_PAGING_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_COLLECTION_ORDERING_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_COLLECTION_FILTERING_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_COLLECTION_ATTRIBUTES_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );
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
     * @param int $itemId
     * @return Mage_Api2_Model_Resource
     */
    protected function _successMessage($message, $code, $itemId = null)
    {
        $this->getResponse()->addMessage($message, $code, $itemId, Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS);
        return $this;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @param int $code
     * @param int $itemId
     * @return Mage_Api2_Model_Resource
     */
    protected function _errorMessage($message, $code, $itemId = null)
    {
        $this->getResponse()->addMessage($message, $code, $itemId, Mage_Api2_Model_Response::MESSAGE_TYPE_ERROR);
        return $this;
    }

    /**
     * Set navigation parameters and apply filters from URL params
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    final protected function _applyCollectionModifiers(Varien_Data_Collection_Db $collection)
    {
        $request    = $this->getRequest();
        $pageNumber = $request->getPageNumber();
        $orderField = $request->getOrderField();

        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }
        if (null !== $orderField) {
            $operation = Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ;
            if (!is_string($orderField)
                || !array_key_exists($orderField, $this->getAvailableAttributes($this->getUserType(), $operation))) {

                $this->_critical(self::RESOURCE_COLLECTION_ORDERING_ERROR);
            }
            $collection->setOrder($orderField, $request->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize(self::DEFAULT_PAGE_SIZE);

        return $this->_applyFilter($collection);
    }

    /**
     * Validate filter data and apply it to collection if possible
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    protected function _applyFilter(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter();

        if (!method_exists($collection, 'addAttributeToFilter') || !$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        $allowedAttributes = $this->getFilter()->getAllowedAttributes(self::OPERATION_ATTRIBUTE_READ);

        foreach ($filter as $filterEntry) {
            if (!is_array($filterEntry)
                || !array_key_exists('attribute', $filterEntry)
                || !in_array($filterEntry['attribute'], $allowedAttributes)) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
            $attributeCode = $filterEntry['attribute'];

            unset($filterEntry['attribute']);

            try {
                $collection->addAttributeToFilter($attributeCode, $filterEntry);
            } catch(Exception $e) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
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
            $this->_multicall = Mage::getModel('api2/multicall');
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
        $request = Mage::getModel('api2/request');

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
        $globalAcl = Mage::getSingleton('api2/acl_global');

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
        $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->getConfig()->getRouteWithSingleTypeAction($this->getResourceType()))
        );
        $params = array(
            'api_type' => $this->getRequest()->getApiType(),
            'id'       => $resource->getId()
        );
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    // TODO: move attributes actions to another external object

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
        $resourceAttrs = $this->_getResourceAttributes();

        // if resource returns not-associative array - attributes' codes only
        if (0 === key($resourceAttrs)) {
            $resourceAttrs = array_combine($resourceAttrs, $resourceAttrs);
        }
        foreach ($resourceAttrs as $attrCode => $attrLabel) {
            if (!isset($available[$attrCode])) {
                $available[$attrCode] = $attrLabel;
            }
        }
        foreach (array_keys($available) as $code) {
            if (in_array($code, $excludedAttrs) || ($includedAttrs && !in_array($code, $includedAttrs))) {
                unset($available[$code]);
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
     * @param bool $onlyVisible Show only the attributes which are visible on frontend
     * @return array
     */
    public function getEavAttributes($onlyVisible = false)
    {
        $attributes = array();
        $model = $this->getConfig()->getResourceWorkingModel($this->getResourceType());

        /** @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('eav/entity_type')->load($model, 'entity_model');

        /** @var $attribute Mage_Eav_Model_Entity_Attribute */
        foreach ($entityType->getAttributeCollection() as $attribute) {
            if ($onlyVisible && !$attribute->getIsVisible()) {
                continue;
            }
            $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
        }

        return $attributes;
    }
}
