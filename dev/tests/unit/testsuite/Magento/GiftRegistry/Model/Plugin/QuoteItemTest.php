<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Plugin;

class QuoteItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Bundle\Model\Plugin\QuoteItem
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->orderItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Item',
            array('setGiftregistryItemId', '__wakeup'),
            array(),
            '',
            false
        );
        $this->quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $this->subjectMock = $this->getMock('Magento\Sales\Model\Convert\Quote', array(), array(), '', false);
        $orderItems = $this->orderItemMock;
        $this->closureMock = function () use ($orderItems) {
            return $orderItems;
        };
        $this->model = new \Magento\GiftRegistry\Model\Plugin\QuoteItem();
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderUsualQuote($registryId)
    {
        $quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array('getQuoteItem', 'getGiftregistryItemId', '__wakeup'),
            array(),
            '',
            false
        );
        $quoteItemMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $this->orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->model->aroundItemToOrderItem($this->subjectMock, $this->closureMock, $quoteItemMock);
        $this->assertSame($this->orderItemMock, $orderItem);
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderQuoteAddress($registryId)
    {
        $quoteItemMock = $this->getMock(
            'Magento\Sales\Model\Quote\Address\Item',
            array('getQuoteItem', 'getGiftregistryItemId', '__wakeup'),
            array(),
            '',
            false
        );
        $stdMock = $this->getMock('stdClass', array('getGiftregistryItemId'), array(), '', false);
        $quoteItemMock->expects($this->once())->method('getQuoteItem')->will($this->returnValue($stdMock));

        $stdMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $this->orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->model->aroundItemToOrderItem($this->subjectMock, $this->closureMock, $quoteItemMock);
        $this->assertSame($this->orderItemMock, $orderItem);
    }

    public function registryIdProvider()
    {
        return array(
            array(false),
            array(2)
        );
    }
}
