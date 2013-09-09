<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftMessage_Model_Plugin_QuoteItemTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Bundle_Model_Plugin_QuoteItem */
    protected $_model;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_invocationChainMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_orderItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_messageFactoryMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_messageMock;

    protected function setUp()
    {
        $this->_orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item', array(), array(), '', false);
        $this->_quoteItemMock = $this->getMock('Magento_Sales_Model_Quote_Item', array(), array(), '', false);
        $this->_messageMock = $this->getMock('Magento_GiftMessage_Model_Message', array(), array(), '', false);
        $this->_invocationChainMock = $this->getMock('Magento_Code_Plugin_InvocationChain',
            array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento_GiftMessage_Helper_Message',
            array('setGiftMessageId', 'isMessagesAvailable'), array(), '', false);
        $this->_messageFactoryMock = $this->getMock('Magento_GiftMessage_Model_MessageFactory',
            array('create'), array(), '', false);
        $this->_model = new Magento_GiftMessage_Model_Plugin_QuoteItem($this->_helperMock, $this->_messageFactoryMock);
    }

    public function testAroundItemToOrderItemReordered()
    {
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $orderMock = $this->getMock('Magento_Sales_Model_Order', array('getReordered'), array(), '', false);
        $this->_orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $orderMock->expects($this->once())->method('getReordered')->will($this->returnValue(true));

        $this->_helperMock->expects($this->exactly(0))->method('isMessagesAvailable');

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);

    }

    public function testAroundItemToOrderItemNotAvailable()
    {
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $this->_orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue(false));
        $this->_orderItemMock->expects($this->exactly(0))->method('getGiftMessageId');

        $this->_helperMock->expects($this->once())->method('isMessagesAvailable')->will($this->returnValue(false));

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);
    }

    public function testAroundItemToOrderItemNoGiftMessage()
    {
        $orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item',
            array('getOrder', 'getGiftMessageId', 'getStoreId', 'getReordered'), array(), '', false);

        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue(false));
        $this->_helperMock->expects($this->once())->method('isMessagesAvailable')->will($this->returnValue(true));
        $orderItemMock->expects($this->once())->method('getGiftMessageId')->will($this->returnValue(false));
        $this->_messageMock->expects($this->never())->method('load');
        $this->_messageFactoryMock->expects($this->never())->method('create');

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($orderItemMock, $orderItem);
    }

    public function testAroundItemToOrderItemWithMessage()
    {
        $orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item',
            array('getOrder', 'getGiftMessageId', 'getStoreId', 'getReordered'), array(), '', false);

        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $orderItemMock->expects($this->once())->method('getOrder')->will($this->returnValue(false));
        $this->_helperMock->expects($this->once())->method('isMessagesAvailable')->will($this->returnValue(true));
        $messageId = 1;
        $orderItemMock->expects($this->once())->method('getGiftMessageId')->will($this->returnValue($messageId));

        $this->_messageMock->expects($this->once())->method('load')->with($messageId)
            ->will($this->returnValue($this->_messageMock));
        $this->_messageMock->expects($this->once())->method('setId')->with(null)
            ->will($this->returnValue($this->_messageMock));
        $this->_messageMock->expects($this->once())->method('save')->will($this->returnValue($this->_messageMock));
        $this->_messageMock->expects($this->once())->method('getId');

        $this->_messageFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_messageMock));

        $quoteItemMock = $this->getMock('Magento_Sales_Model_Quote_Item',
            array('setGiftMessageId'), array(), '', false);
        $quoteItemMock->expects($this->once())->method('setGiftMessageId');

        $orderItem = $this->_model->aroundItemToOrderItem(array($quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($orderItemMock, $orderItem);
    }
}