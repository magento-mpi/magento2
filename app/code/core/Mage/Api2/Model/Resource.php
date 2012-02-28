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
 * API2 Resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Resource
{
    /**#@+
     * Operations. Resource method names
     */
    const OPERATION_CREATE   = 'create';
    const OPERATION_RETRIEVE = 'retrieve';
    const OPERATION_UPDATE   = 'update';
    const OPERATION_DELETE   = 'delete';
    /**#@- */

    /**#@+
     * Common operations for attributes
     */
    const OPERATION_ATTRIBUTE_READ  = 'read';
    const OPERATION_ATTRIBUTE_WRITE = 'write';
    /**#@- */

    /**#@+
     *  Default error messages
     */
    const RESOURCE_NOT_FOUND = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR = 'Resource data pre-validation error.'; //error while pre-validating
    const RESOURCE_DATA_INVALID = 'Resource data invalid.'; //error while checking data inside method
    const RESOURCE_UNKNOWN_ERROR = 'Resource unknown error.';
    /**#@- */

    /**#@+
     *  Default success messages
     */
    const RESOURCE_UPDATED_SUCCESSFUL = 'Resource updated successful.';
    /**#@- */

    /**
     * Api user
     *
     * @var Mage_Api2_Model_Auth_User_Abstract
     */
    protected $_apiUser;

    /**
     * Attribute Filter
     *
     * @var  Mage_Api2_Model_Acl_Filter
     */
    protected $_filter;

    /**
     * Request
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

    /**
     * Response
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Renderer
     *
     * @var Mage_Api2_Model_Renderer_Interface
     */
    protected $_renderer;

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
     * User type
     *
     * @var string
     */
    protected $_userType;

    /**
     * API Version
     *
     * @var string
     */
    protected $_version;

    /**
     * Internal resource model dispatch
     */
    abstract public function dispatch();

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
     * @return array
     */
    protected function _retrieve()
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
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
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
     * Validate input data for self::create() or self::update() depends on the kind of resource:
     *   Mage_Api2_Model_Resource_Collection::create()
     *   Mage_Api2_Model_Resource_Instance::update()
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                $this->_error(sprintf('Missing "%s" in request.', $key), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                continue;
            }
        }

        foreach ($notEmpty as $key) {
            if (array_key_exists($key, $data) && empty($data[$key])) {
                $this->_error(
                    sprintf('Empty value for "%s" in request.', $key), Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
            }
        }

        if ($this->getResponse()->isException()) {
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
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
            self::RESOURCE_NOT_FOUND => Mage_Api2_Model_Server::HTTP_NOT_FOUND,
            self::RESOURCE_METHOD_NOT_ALLOWED => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_METHOD_NOT_IMPLEMENTED => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_DATA_PRE_VALIDATION_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_INTERNAL_ERROR => Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
            self::RESOURCE_UNKNOWN_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
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
     */
    public function setApiUser(Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $this->_apiUser = $apiUser;
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
     */
    public function setRequest(Mage_Api2_Model_Request $request)
    {
        $this->setResourceType($request->getResourceType());
        $this->setApiType($request->getApiType());
        $this->setVersion($request->getVersion());

        $this->_request = $request;
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
     */
    public function setResourceType($resourceType)
    {
        $this->_resourceType = $resourceType;
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
     */
    public function setApiType($apiType)
    {
        $this->_apiType = $apiType;
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
     */
    public function setUserType($userType)
    {
        $this->_userType = $userType;
    }

    /**
     * Get API version
     * If not exists get from Request
     *
     * @return int
     */
    public function getVersion()
    {
        if (!$this->_version) {
            $this->setVersion($this->getRequest()->getVersion());
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
     * Get API2 config
     *
     * @return Mage_Api2_Model_Config
     */
    public function getConfig()
    {
        return Mage::getModel('api2/config');
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
     * Get available attributes of API resource
     *
     * @param string $userType
     * @param string $operation
     * @return array
     */
    public function getAvailableAttributes($userType, $operation)
    {
        $available     = array();
        $configAttrs   = $this->getAvailableAttributesFromConfig();
        $excludedAttrs = $this->getExcludedAttributes($userType, $operation);
        $dbAttrs = $this->getDbAttributes();
        $allAttrs = array_merge($configAttrs, $dbAttrs);

        foreach ($allAttrs as $code) {
            if (in_array($code, $excludedAttrs)) {
                continue;
            }
            $available[$code] = isset($configAttrs[$code]) ? $configAttrs[$code] : $code;
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
     * Get available attributes of API resource from configuration file
     *
     * @return array
     * @throw Exception
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
        $available     = array();
        /* @var $resource Mage_Core_Model_Resource_Db_Abstract */
        $resource = Mage::getResourceModel($this->getConfig()->getResourceWorkingModel($this->getResourceType()));
        if (method_exists($resource, 'getMainTable')) {
            $available = array_keys($resource->getReadConnection()->describeTable($resource->getMainTable()));
        }
        return $available;
    }

    /**
     * Get EAV attributes of working model
     *
     * @return array
     */
    public function getEavAttributes()
    {
        $model = $this->getConfig()->getResourceWorkingModel($this->getResourceType());

        /** @var $entityType Mage_Eav_Model_Entity_Type */
        $entityType = Mage::getModel('eav/entity_type')->load($model, 'entity_model');

        /** @var $resourceModel Mage_Eav_Model_Resource_Entity_Attribute_Collection */
        $resourceModel = Mage::getResourceModel($entityType->getEntityAttributeCollection());
        $attributesInfo = $resourceModel
            ->setEntityTypeFilter($entityType)
            ->getData();

        $attributes = array();
        foreach ($attributesInfo as $attribute) {
            $attributes[$attribute['attribute_code']] = $attribute['frontend_label'];
        }

        return $attributes;
    }
}
