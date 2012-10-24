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
 * Abstract API request.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Webapi_Controller_RequestAbstract extends Zend_Controller_Request_Http
{
    /**#@+
     * Name of query ($_GET) parameters to use in navigation and so on
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
     * Create request object. Factory method for API requests.
     *
     * @param string $apiType
     * @return Mage_Webapi_Controller_RequestAbstract
     * @throws InvalidArgumentException If API type is undefined in Mage_Webapi_Controller_Front_Base
     */
    public static function createRequest($apiType)
    {
        switch($apiType) {
            case Mage_Webapi_Controller_Front_Base::API_TYPE_REST:
                return new Mage_Webapi_Controller_Request_Rest();
                break;
            case Mage_Webapi_Controller_Front_Base::API_TYPE_SOAP:
                return new Mage_Webapi_Controller_Request_Soap();
                break;
            default:
                throw new InvalidArgumentException('The "%s" API type is not valid.', $apiType);
                break;
        }
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
