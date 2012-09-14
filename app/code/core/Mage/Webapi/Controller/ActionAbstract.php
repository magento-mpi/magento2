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
 * Generic API action controller.
 */
abstract class Mage_Webapi_Controller_ActionAbstract
{
    /**#@+
     * Default message types.
     */
    const MESSAGE_TYPE_SUCCESS = 'success';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_WARNING = 'warning';
    /**#@-*/

    /**#@+
     * Collection page sizes.
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX = 100;
    /**#@-*/

    /** @var Mage_Webapi_Model_Request */
    protected $_request;

    /** @var Mage_Webapi_Model_Response */
    protected $_response;

    /** @var Mage_Webapi_Helper_Data */
    protected $_translationHelper;

    /**
     * Initialize dependencies.
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param Mage_Core_Helper_Abstract $translationHelper
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response,
        Mage_Core_Helper_Abstract $translationHelper
    ) {
        $this->_translationHelper = $translationHelper ? $translationHelper : Mage::helper('Mage_Webapi_Helper_Data');
        $this->_request = $request;
        $this->_response = $response;
    }

    /**
     * Retrieve request.
     *
     * @return Mage_Webapi_Model_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Set request.
     *
     * @param Mage_Webapi_Model_Request $request
     * @return Mage_Webapi_Controller_ActionAbstract
     */
    public function setRequest(Mage_Webapi_Model_Request $request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * Retrieve response.
     *
     * @return Mage_Webapi_Model_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set response.
     *
     * @param Mage_Webapi_Model_Response $response
     * @return Mage_Webapi_Controller_ActionAbstract
     */
    public function setResponse(Mage_Webapi_Model_Response $response)
    {
        $this->_response = $response;
        return $this;
    }

    /**
     * Add non-critical error
     *
     * @param string $message
     * @param int $code
     * @return Mage_Webapi_Controller_ActionAbstract
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
     * @return Mage_Webapi_Controller_ActionAbstract
     */
    protected function _successMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, self::MESSAGE_TYPE_SUCCESS);
        return $this;
    }

    /**
     * Add error message
     *
     * @param string $message
     * @param int $code
     * @param array $params
     * @return Mage_Webapi_Controller_ActionAbstract
     */
    protected function _errorMessage($message, $code, $params = array())
    {
        $this->getResponse()->addMessage($message, $code, $params, self::MESSAGE_TYPE_ERROR);
        return $this;
    }

    /**
     * Set navigation parameters and apply filters from URL params.
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Varien_Data_Collection_Db
     * @throws RuntimeException
     */
    // TODO: Check and finish this method
    final protected function _applyCollectionModifiers(Varien_Data_Collection_Db $collection)
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            throw new RuntimeException($this->_translationHelper->__("Page number is invalid."));
        }
        $pageSize = $this->getRequest()->getPageSize();
        if (null == $pageSize) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                throw new RuntimeException($this->_translationHelper
                    ->__('The paging limit exceeds the allowed number.'));
            }
        }
        $orderField = $this->getRequest()->getOrderField();
        if (null !== $orderField) {
            if (!is_string($orderField)
                // TODO: Check if order field is allowed for specified entity
            ) {
                throw new RuntimeException($this->_translationHelper
                    ->__('Collection "order" value is invalid.'));
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        return $collection;
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
}
