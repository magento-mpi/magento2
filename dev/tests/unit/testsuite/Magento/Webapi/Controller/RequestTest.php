<?php
/**
 * Test for Webapi Request.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Request object.
     *
     * @var Magento_Webapi_Controller_Request
     */
    protected $_request;

    protected function setUp()
    {
        parent::setUp();

        $this->_request = new Magento_Webapi_Controller_RequestStub(Magento_Webapi_Controller_Front::API_TYPE_REST);
    }

    /**
     * Test for getFilter() method.
     */
    public function testGetFilter()
    {
        $_POST[Magento_Webapi_Controller_Request::QUERY_PARAM_FILTER] = 'filter_exists';
        $this->_request->setParam(Magento_Webapi_Controller_Request::QUERY_PARAM_FILTER, 'filter_exists');

        $this->assertNull($this->_request->getFilter());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_FILTER] = 'filter_exists';

        $this->assertEquals('filter_exists', $this->_request->getFilter());
    }

    /**
     * Test for getOrderDirection() method.
     */
    public function testGetOrderDirection()
    {
        $_POST[Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_DIR] = 'asc';
        $this->_request->setParam(Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_DIR, 'asc');

        $this->assertNull($this->_request->getOrderDirection());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_DIR] = 'asc';

        $this->assertEquals('asc', $this->_request->getOrderDirection());
    }

    /**
     * Test for getOrderField() method.
     */
    public function testGetOrderField()
    {
        $_POST[Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_FIELD] = 'order_exists';
        $this->_request->setParam(Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_FIELD, 'order_exists');

        $this->assertNull($this->_request->getOrderField());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_ORDER_FIELD] = 'order_exists';

        $this->assertEquals('order_exists', $this->_request->getOrderField());
    }

    /**
     * Test for getPageNumber() method.
     */
    public function testGetPageNumber()
    {
        $_POST[Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_NUM] = 5;
        $this->_request->setParam(Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_NUM, 5);

        $this->assertNull($this->_request->getPageNumber());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_NUM] = 5;

        $this->assertEquals(5, $this->_request->getPageNumber());
    }

    /**
     * Test for getPageSize() method.
     */
    public function testGetPageSize()
    {
        $_POST[Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_SIZE] = 5;
        $this->_request->setParam(Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_SIZE, 5);
        $this->assertNull($this->_request->getPageSize());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_PAGE_SIZE] = 5;
        $this->assertEquals(5, $this->_request->getPageSize());
    }

    /**
     * Test for getRequestedAttributes() method.
     */
    public function testGetRequestedAttributes()
    {
        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_REQ_ATTRS][] = 'attr1';
        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_REQ_ATTRS][] = 'attr2';

        $this->assertInternalType('array', $this->_request->getRequestedAttributes());
        $this->assertEquals(array('attr1', 'attr2'), $this->_request->getRequestedAttributes());

        $_GET[Magento_Webapi_Controller_Request::QUERY_PARAM_REQ_ATTRS] = 'attr1, attr2';

        $this->assertInternalType('array', $this->_request->getRequestedAttributes());
        $this->assertEquals(array('attr1', 'attr2'), $this->_request->getRequestedAttributes());
    }
}

class Magento_Webapi_Controller_RequestStub extends Magento_Webapi_Controller_Request
{
    public function getRequestedResources()
    {
        return array();
    }
}
