<?php
/**
 * Web API request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class Request extends \Zend_Controller_Request_Http
{
    const PARAM_API_TYPE = 'api_type';

    /**#@+
     * Name of query ($_GET) parameters to use in navigation and so on.
     */
    const QUERY_PARAM_REQ_ATTRS = 'attrs';
    const QUERY_PARAM_PAGE_NUM = 'page';
    const QUERY_PARAM_PAGE_SIZE = 'limit';
    const QUERY_PARAM_ORDER_FIELD = 'order';
    const QUERY_PARAM_ORDER_DIR = 'dir';
    const QUERY_PARAM_FILTER = 'filter';
    /**#@-*/

    /** @var string */
    protected $_apiType;

    /**
     * Set current API type.
     *
     * @param string $apiType
     * @param null|string|Zend_Uri $uri
     */
    public function __construct($apiType, $uri = null)
    {
        $this->setApiType($apiType);
        parent::__construct($uri);
    }

    /**
     * Get current API type.
     *
     * @return string
     */
    public function getApiType()
    {
        return $this->_apiType;
    }

    /**
     * Set current API type.
     *
     * @param string $apiType
     */
    public function setApiType($apiType)
    {
        $this->_apiType = $apiType;
    }

    /**
     * Get filter settings passed by API user.
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->getQuery(self::QUERY_PARAM_FILTER);
    }

    /**
     * Get sort order direction requested by API user.
     *
     * @return mixed
     */
    public function getOrderDirection()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_DIR);
    }

    /**
     * Get sort order field requested by API user.
     *
     * @return mixed
     */
    public function getOrderField()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_FIELD);
    }

    /**
     * Retrieve page number requested by API user.
     *
     * @return mixed
     */
    public function getPageNumber()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_NUM);
    }

    /**
     * Retrieve page size requested by API user.
     *
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_SIZE);
    }

    /**
     * Get an array of attribute codes requested by API user.
     *
     * @return array
     */
    public function getRequestedAttributes()
    {
        $include = $this->getQuery(self::QUERY_PARAM_REQ_ATTRS, array());

        //transform comma-separated list
        if (!is_array($include)) {
            $include = explode(',', $include);
        }
        return array_map('trim', $include);
    }
}
