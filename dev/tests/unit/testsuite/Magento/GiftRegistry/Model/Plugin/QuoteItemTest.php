<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftRegistry_Model_Plugin_QuoteItemTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Bundle\Model\Plugin\QuoteItem */
    protected $_model;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_invocationChainMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_orderItemMock;

    protected function setUp()
    {
        $this->_orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', array(), array(), '', false);
        $this->_quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $this->_invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain',
            array(), array(), '', false);
        $this->_model = new \Magento\GiftRegistry\Model\Plugin\QuoteItem();
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderUsualQuote($registryId)
    {
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item',
            array('setGiftregistryItemId'), array(), '', false);
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item',
            array('getQuoteItem', 'getGiftregistryItemId'), array(), '', false);
        $quoteItemMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->_model->aroundItemToOrderItem(array($quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($orderItemMock, $orderItem);
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderQuoteAddress($registryId)
    {
        $orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item',
            array('setGiftregistryItemId'), array(), '', false);
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Address\Item',
            array('getQuoteItem', 'getGiftregistryItemId'), array(), '', false);
        $stdMock = $this->getMock('stdClass', array('getGiftregistryItemId'), array(), '', false);
        $quoteItemMock->expects($this->once())->method('getQuoteItem')->will($this->returnValue($stdMock));

        $stdMock->expects($this->once())->method('getGiftregistryItemId')->will($this->returnValue($registryId));

        if ($registryId) {
            $orderItemMock->expects($this->once())->method('setGiftregistryItemId')->with($registryId);
        }
        $orderItem = $this->_model->aroundItemToOrderItem(array($quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($orderItemMock, $orderItem);
    }

    public function registryIdProvider()
    {
        return array(
            array(false),
            array(2)
        );
    }
}
