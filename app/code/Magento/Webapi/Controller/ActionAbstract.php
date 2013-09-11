<?php
/**
 * Generic action controller for all resources available via web API.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

abstract class ActionAbstract
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

    /** @var \Magento\Webapi\Controller\Request */
    protected $_request;

    /** @var \Magento\Webapi\Controller\Response */
    protected $_response;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Request\Factory $requestFactory
     * @param \Magento\Webapi\Controller\Response\Factory $responseFactory
     */
    public function __construct(
        \Magento\Webapi\Controller\Request\Factory $requestFactory,
        \Magento\Webapi\Controller\Response\Factory $responseFactory
    ) {
        $this->_request = $requestFactory->get();
        $this->_response = $responseFactory->get();
    }

    /**
     * Retrieve request.
     *
     * @return \Magento\Webapi\Controller\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Retrieve response.
     *
     * @return \Magento\Webapi\Controller\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Set navigation parameters and apply filters from URL params.
     *
     * @param \Magento\Data\Collection\Db $collection
     * @return \Magento\Data\Collection\Db
     * @throws \Magento\Webapi\Exception
     */
    // TODO: Check and finish this method (the implementation was migrated from Magento 1)
    final protected function _applyCollectionModifiers(\Magento\Data\Collection\Db $collection)
    {
        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            throw new \Magento\Webapi\Exception(
                __("Page number is invalid."),
                \Magento\Webapi\Exception::HTTP_BAD_REQUEST
            );
        }
        $pageSize = $this->getRequest()->getPageSize();
        if (null == $pageSize) {
            $pageSize = self::PAGE_SIZE_DEFAULT;
        } else {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                throw new \Magento\Webapi\Exception(
                    __('The paging limit exceeds the allowed number.'),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST
                );
            }
        }
        $orderField = $this->getRequest()->getOrderField();
        if (null !== $orderField) {
            if (!is_string($orderField)
                // TODO: Check if order field is allowed for specified entity
            ) {
                throw new \Magento\Webapi\Exception(
                    __('Collection "order" value is invalid.'),
                    \Magento\Webapi\Exception::HTTP_BAD_REQUEST
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
