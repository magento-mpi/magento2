<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftMessage_Model_Plugin_QuoteItemTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Model\Plugin\QuoteItem */
    protected $_model;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_invocationChainMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_orderItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    protected function setUp()
    {
        $this->_orderItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Item',
            array('setGiftMessageId', 'setGiftMessageAvailable'),
            array(),
            '',
            false
        );
        $this->_quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array('getGiftMessageId', 'getStoreId'),
            array(),
            '',
            false
        );
        $this->_invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain',
            array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento\GiftMessage\Helper\Message',
            array('setGiftMessageId', 'isMessagesAvailable'), array(), '', false);
        $this->_model = new \Magento\GiftMessage\Model\Plugin\QuoteItem($this->_helperMock);
    }

    public function testAroundItemToOrderItem()
    {
        $storeId = 1;
        $giftMessageId = 1;
        $isMessageAvailable = true;

        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));
        $this->_quoteItemMock->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $this->_quoteItemMock->expects($this->any())
            ->method('getGiftMessageId')
            ->will($this->returnValue($giftMessageId));

        $this->_helperMock->expects($this->once())->method('isMessagesAvailable')
            ->with('item', $this->_quoteItemMock, $storeId)
            ->will($this->returnValue($isMessageAvailable));
        $this->_orderItemMock->expects($this->once())
            ->method('setGiftMessageId')
            ->with($giftMessageId);
        $this->_orderItemMock->expects($this->once())
            ->method('setGiftMessageAvailable')
            ->with($isMessageAvailable);

        $this->assertSame(
            $this->_orderItemMock,
            $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock)
        );

    }
}
