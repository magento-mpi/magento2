<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_urlBuilder = $this->getMock('Magento\Backend\Model\Url', ['getUrl'], [], '', false);
        $contextMock = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $contextMock->expects($this->any())->method('getUrlBuilder')->will($this->returnValue($this->_urlBuilder));
        $this->_orderFactory = $this->getMock('Magento\Sales\Model\OrderFactory', ['create'], [], '', false);
        $this->_model = new Backend(
            $contextMock,
            $this->getMock('Magento\Store\Model\StoreManager', [], [], '', false),
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
            $this->equalTo([])
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals('some value', $this->_model->getPlaceOrderAdminUrl());
    }

    public function testGetSuccessOrderUrl()
    {
        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['loadByIncrementId', 'getId', '__wakeup'],
            [],
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
            $this->equalTo(['order_id' => 'order id'])
        )->will(
            $this->returnValue('some value')
        );
        $this->assertEquals(
            'some value',
            $this->_model->getSuccessOrderUrl(['x_invoice_num' => 'invoice number', 'some param'])
        );
    }

    public function testGetRedirectIframeUrl()
    {
        $params = ['some params'];
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
