<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Helper;

class BackendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Backend
     */
    protected $_model;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_orderFactory;

    protected function setUp()
    {
        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', array('getUrl'), array(), '', false);
        $contextMock = $this->getMock('Magento\App\Helper\Context', array(), array(), '', false);
        $contextMock->expects($this->any())->method('getUrlBuilder')->will($this->returnValue($this->_urlBuilder));
        $this->_orderFactory = $this->getMock('Magento\Sales\Model\OrderFactory', array('create'), array(), '', false);
        $this->_model = new Backend(
            $contextMock,
            $this->getMock('Magento\Core\Model\StoreManager', array(), array(), '', false),
            $this->_orderFactory,
            $this->_urlBuilder
        );
    }

    public function testGetPlaceOrderAdminUrl()
    {
        $this->_urlBuilder->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            $this->equalTo('adminhtml/authorizenet_directpost_payment/place'),
            $this->equalTo(array())
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals('some value', $this->_model->getPlaceOrderAdminUrl());
    }

    public function testGetSuccessOrderUrl()
    {
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            array('loadByIncrementId', 'getId', '__wakeup'),
            array(),
            '',
            false
        );
        $order->expects($this->once())->method('loadByIncrementId')->with('invoice number')->will($this->returnSelf());
        $order->expects($this->once())->method('getId')->will($this->returnValue('order id'));
        $this->_orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        $this->_urlBuilder->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            $this->equalTo('sales/order/view'),
            $this->equalTo(array('order_id' => 'order id'))
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals(
            'some value',
            $this->_model->getSuccessOrderUrl(array('x_invoice_num' => 'invoice number', 'some param'))
        );
    }

    public function testGetRedirectIframeUrl()
    {
        $params = array('some params');
        $this->_urlBuilder->expects(
            $this->once()
        )->method(
            'getUrl'
        )->with(
            $this->equalTo('adminhtml/authorizenet_directpost_payment/redirect'),
            $this->equalTo($params)
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals('some value', $this->_model->getRedirectIframeUrl($params));
    }
}
