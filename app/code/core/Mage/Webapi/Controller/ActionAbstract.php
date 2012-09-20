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
     * Collection page sizes.
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX = 100;
    /**#@-*/

    /** @var Mage_Webapi_Controller_RequestAbstract */
    protected $_request;

    /** @var Mage_Webapi_Controller_Response */
    protected $_response;

    /** @var Mage_Webapi_Helper_Data */
    protected $_translationHelper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_RequestAbstract $request
     * @param Mage_Webapi_Controller_Response $response
     * @param Mage_Core_Helper_Abstract $translationHelper
     */
    public function __construct(Mage_Webapi_Controller_RequestAbstract $request,
        Mage_Webapi_Controller_Response $response, Mage_Core_Helper_Abstract $translationHelper = null
    ) {
        $this->_translationHelper = $translationHelper ? $translationHelper : Mage::helper('Mage_Webapi_Helper_Data');
        $this->_request = $request;
        $this->_response = $response;
    }

    /**
     * Retrieve request.
     *
     * @return Mage_Webapi_Controller_RequestAbstract
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response.
     *
     * @return Mage_Webapi_Controller_Response
     */
    public function getResponse()
    {
        return $this->_response;
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
