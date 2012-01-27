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
 * Base class for all API resources
 */
abstract class Mage_Api2_Model_Resource
{
    /**#@+
     * Operations. Resource method names
     */
    const OPERATION_CREATE   = '_create';
    const OPERATION_RETRIEVE = '_retrieve';
    const OPERATION_UPDATE   = '_update';
    const OPERATION_DELETE   = '_delete';
    /**#@- */

    /**#@+
     *  Default error messages
     */
    const RESOURCE_NOT_FOUND                  = 'Resource not found.';
    const RESOURCE_METHOD_NOT_ALLOWED         = 'Resource does not support method.';
    const RESOURCE_METHOD_NOT_IMPLEMENTED     = 'Resource method not implemented yet.';
    const RESOURCE_INTERNAL_ERROR             = 'Resource internal error.';
    const RESOURCE_DATA_PRE_VALIDATION_ERROR  = 'Resource data pre-validation error.'; //error while pre-validating
    const RESOURCE_DATA_INVALID               = 'Resource data invalid.'; //error while checking data inside method
    const RESOURCE_UNKNOWN_ERROR              = 'Resource unknown error.';
    /**#@- */

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR     = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_ORDERING_ERROR   = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR  = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR = 'Resource collection including additional attributes error.';
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
     * Render data using registered Renderer
     *
     * @param mixed $data
     * @return void
     */
    protected function _render($data)
    {
        $response = $this->getResponse();

        $response->clearHeaders()
            ->setBody($this->getRenderer()->render($data))
            ->setHeader(
                'Content-Type',
                sprintf(
                    '%s; charset=%s',
                    $this->getRenderer()->getMimeType(),
                    Mage_Api2_Model_Response::RESPONSE_CHARSET
                )
            );
    }

    /**
     * Validate input data for self::create() or self::update() depends on the kind of resource:
     *   Mage_Api2_Model_Resource_Collection::create()
     *   Mage_Api2_Model_Resource_Instance::update()
     *
     * @param array $data
     * @param array $required
     * @param array $valueable
     */
    protected function _validate(array $data, array $required = array(), array $valueable = array())
    {
        //NOTE can be extended in subclasses
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                $this->_error(sprintf('Missing "%s" in request.', $key), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                continue;
            }

            if (array_key_exists($key, $valueable) && empty($data[$key])) {
                $this->_error(
                    sprintf('Empty value for "%s" in request.', $key), Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
            }
        }

        if ($this->getResponse()->isException()) {
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
    }

    //Errors

    /**
     * Throw exception, critical error - stop execution
     *
     * @param string $message
     * @param int $code
     * @throws Mage_Api2_Exception
     */
    protected function _critical($message, $code = null)
    {
        $errors = array(
            self::RESOURCE_NOT_FOUND                 => Mage_Api2_Model_Server::HTTP_NOT_FOUND,
            self::RESOURCE_METHOD_NOT_ALLOWED        => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_METHOD_NOT_IMPLEMENTED    => Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED,
            self::RESOURCE_DATA_PRE_VALIDATION_ERROR => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            self::RESOURCE_INTERNAL_ERROR            => Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR,
            self::RESOURCE_UNKNOWN_ERROR             => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
        );

        //Collections
        $errors[self::RESOURCE_COLLECTION_PAGING_ERROR]     = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_ORDERING_ERROR]   = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_FILTERING_ERROR]  = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_ATTRIBUTES_ERROR] = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;

        //Instances
        //$errors[self::RESOURCE_INSTANCE_..._ERROR] = Mage_Api2_Model_Server::HTTP_...;

        if ($code === null) {
            if (!isset($errors[$message])) {
                throw new Mage_Api2_Exception(
                    sprintf('Invalid error "%s" or error code missed.', $message),
                    Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR
                );
            }
            $code = $errors[$message];
        }

        throw new Mage_Api2_Exception($message, $code);
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

    //CRUD

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

    //Setters/getters

    /**
     * Get filter if not exists create
     *
     * @return Mage_Api2_Model_Acl_Filter
     */
    public function getFilter()
    {
        if (!$this->_filter) {
            /** @var $filter Mage_Api2_Model_Acl_Filter */
            $filter = Mage::getSingleton('api2/acl_filter');
            $filter->setResourceType($this->getRequest()->getResourceType())
                   ->setUserType($this->getApiUser()->getType())
                   ->setInclude($this->getRequest()->getInclude());

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
}
