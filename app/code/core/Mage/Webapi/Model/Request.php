<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API Request model
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Request extends Zend_Controller_Request_Http
{
    /**#@+
     * Name of query ($_GET) parameters to use in navigation and so on
     */
    const QUERY_PARAM_REQ_ATTRS   = 'attrs';
    const QUERY_PARAM_PAGE_NUM    = 'page';
    const QUERY_PARAM_PAGE_SIZE   = 'limit';
    const QUERY_PARAM_ORDER_FIELD = 'order';
    const QUERY_PARAM_ORDER_DIR   = 'dir';
    const QUERY_PARAM_FILTER      = 'filter';
    /**#@- */

    /**
     * Constructor
     *
     * If a $uri is passed, the object will attempt to populate itself using
     * that information.
     * Override parent class to allow object instance get via Mage::getSingleton()
     *
     * @param string|Zend_Uri $uri
     */
    public function __construct($uri = null)
    {
        parent::__construct($uri ? $uri : null);
    }

    /**
     * Get api type from Request
     *
     * @return string
     */
    public function getApiType()
    {
        // getParam() is not used to avoid parameter fetch from $_GET or $_POST
        return isset($this->_params['api_type']) ? $this->_params['api_type'] : null;
    }

    /**
     * Get filter settings passed by API user
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->getQuery(self::QUERY_PARAM_FILTER);
    }

    /**
     * Get sort order direction requested by API user
     *
     * @return mixed
     */
    public function getOrderDirection()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_DIR);
    }

    /**
     * Get sort order field requested by API user
     *
     * @return mixed
     */
    public function getOrderField()
    {
        return $this->getQuery(self::QUERY_PARAM_ORDER_FIELD);
    }

    /**
     * Retrieve page number requested by API user
     *
     * @return mixed
     */
    public function getPageNumber()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_NUM);
    }

    /**
     * Retrieve page size requested by API user
     *
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->getQuery(self::QUERY_PARAM_PAGE_SIZE);
    }

    /**
     * Get an array of attribute codes requested by API user
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
