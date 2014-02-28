<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Model\Plugin;

class QuoteItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Plugin\QuoteItem
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->orderItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Item',
            array('setGiftMessageId', 'setGiftMessageAvailable', '__wakeup'),
            array(),
            '',
            false
        );
        $this->quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array('getGiftMessageId', 'getStoreId', '__wakeup'),
            array(),
            '',
            false
        );
        $orderItems = $this->orderItemMock;
        $this->closureMock = function () use ($orderItems) {
            return $orderItems;
        };
        $this->subjectMock = $this->getMock('Magento\Sales\Model\Convert\Quote', array(), array(), '', false);
        $this->helperMock = $this->getMock('Magento\GiftMessage\Helper\Message',
            array('setGiftMessageId', 'isMessagesAvailable'), array(), '', false);
        $this->model = new \Magento\GiftMessage\Model\Plugin\QuoteItem($this->helperMock);
    }

    public function testAroundItemToOrderItem()
    {
        $storeId = 1;
        $giftMessageId = 1;
        $isMessageAvailable = true;

        $this->quoteItemMock->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));
        $this->quoteItemMock->expects($this->any())
            ->method('getGiftMessageId')
            ->will($this->returnValue($giftMessageId));

        $this->helperMock->expects($this->once())->method('isMessagesAvailable')
            ->with('item', $this->quoteItemMock, $storeId)
            ->will($this->returnValue($isMessageAvailable));
        $this->orderItemMock->expects($this->once())
            ->method('setGiftMessageId')
            ->with($giftMessageId);
        $this->orderItemMock->expects($this->once())
            ->method('setGiftMessageAvailable')
            ->with($isMessageAvailable);

        $this->assertSame(
            $this->orderItemMock,
            $this->model->aroundItemToOrderItem($this->subjectMock, $this->closureMock, $this->quoteItemMock)
        );

    }
}
