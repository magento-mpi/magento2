<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Plugin_QuoteItemProductOptionTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_invocationChainMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_orderItemMock;

    /** @var \Magento\Catalog\Model\Plugin\QuoteItemProductOption */
    protected $_model;

    protected function setUp()
    {
        $this->_orderItemMock = $this->getMock('Magento\Sales\Model\Order\Item', array(), array(), '', false);
        $this->_quoteItemMock = $this->getMock('Magento\Sales\Model\Quote\Item', array(), array(), '', false);
        $this->_invocationChainMock = $this->getMock('Magento\Code\Plugin\InvocationChain',
            array(), array(), '', false);

        $this->_model = new \Magento\Catalog\Model\Plugin\QuoteItemProductOption();
    }

    public function testAroundItemToOrderItemEmptyOptions()
    {
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $this->_quoteItemMock->expects($this->exactly(2))->method('getOptions')
            ->will($this->returnValue(array()));

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);
    }

    public function testAroundItemToOrderItemWithOptions()
    {
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $itemOption = $this->getMock('Magento\Sales\Model\Quote\Item\Option', array('getCode'), array(), '', false);
        $this->_quoteItemMock->expects($this->exactly(2))->method('getOptions')
            ->will($this->returnValue(array($itemOption, $itemOption)));

        $itemOption->expects($this->at(0))->method('getCode')->will($this->returnValue('someText_8'));
        $itemOption->expects($this->at(1))->method('getCode')->will($this->returnValue('not_int_text'));

        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $optionMock = $this->getMock('stdClass', array('getType'));
        $optionMock->expects($this->once())->method('getType');

        $productMock->expects($this->once())->method('getOptionById')->will($this->returnValue($optionMock));

        $this->_quoteItemMock->expects($this->once())->method('getProduct')
            ->will($this->returnValue($productMock));

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);
    }
}
