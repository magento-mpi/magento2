<?php
/**
 * Generic action controller for all resources available via web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Magento_Webapi_Controller_ActionAbstract
{
    /**#@+
     * Collection page sizes.
     */
    const PAGE_SIZE_DEFAULT = 10;
    const PAGE_SIZE_MAX = 100;
    /**#@-*/

    /**#@+
     * Allowed API resource methods.
     */
    const METHOD_CREATE = 'create';
    const METHOD_GET = 'get';
    const METHOD_LIST = 'list';
    const METHOD_UPDATE = 'update';
    const METHOD_DELETE = 'delete';
    const METHOD_MULTI_UPDATE = 'multiUpdate';
    const METHOD_MULTI_DELETE = 'multiDelete';
    const METHOD_MULTI_CREATE = 'multiCreate';
    /**#@-*/

    /** @var Magento_Webapi_Controller_Request */
    protected $_request;

    /** @var Magento_Webapi_Controller_Response */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Request_Factory $requestFactory
     * @param Magento_Webapi_Controller_Response_Factory $responseFactory
     */
    public function __construct(
        Magento_Webapi_Controller_Request_Factory $requestFactory,
        Magento_Webapi_Controller_Response_Factory $responseFactory
    ) {
        $this->_request = $requestFactory->get();
        $this->_response = $responseFactory->get();
    }

    /**
     * Retrieve request.
     *
     * @return Magento_Webapi_Controller_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response.
     *
     * @return Magento_Webapi_Controller_Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set navigation parameters and apply filters from URL params.
     *
     * @param Magento_Data_Collection_Db $collection
     * @return Magento_Data_Collection_Db
     * @throws Magento_Webapi_Exception
     */
    // TODO: Check and finish this method (the implementation was migrated from Magento 1)
    final protected function _applyCollectionModifiers(Magento_Data_Collection_Db $collection)
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            throw new Magento_Webapi_Exception(
                __("Page number is invalid."),
                Magento_Webapi_Exception::HTTP_BAD_REQUEST
            );
        }
        $pageSize = $this->getRequest()->getPageSize();
        if (null == $pageSize) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                throw new Magento_Webapi_Exception(
                    __('The paging limit exceeds the allowed number.'),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }
        }
        $orderField = $this->getRequest()->getOrderField();
        if (null !== $orderField) {
            if (!is_string($orderField)
                // TODO: Check if order field is allowed for specified entity
            ) {
                throw new Magento_Webapi_Exception(
                    __('Collection "order" value is invalid.'),
                    Magento_Webapi_Exception::HTTP_BAD_REQUEST
                );
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        return $collection;
    }

    /**
     * Check if specified action is defined in current controller.
     *
     * @param string $actionName
     * @return bool
     */
    public function hasAction($actionName)
    {
        return method_exists($this, $actionName);
    }

    /**
     * Retrieve list of allowed method names in action controllers.
     *
     * @return array
     */
    public static function getAllowedMethods()
    {
        return array(
            self::METHOD_CREATE,
            self::METHOD_GET,
            self::METHOD_LIST,
            self::METHOD_UPDATE,
            self::METHOD_MULTI_UPDATE,
            self::METHOD_DELETE,
            self::METHOD_MULTI_DELETE,
            self::METHOD_MULTI_CREATE,
        );
    }
}
