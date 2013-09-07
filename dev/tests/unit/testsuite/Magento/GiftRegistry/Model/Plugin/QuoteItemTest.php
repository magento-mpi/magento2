<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Model_Plugin_QuoteItemTest extends Magento_Bundle_Model_Plugin_QuoteItemParent
{
    protected function setUp()
    {
        parent::setUp();
        $this->_model = new Magento_GiftRegistry_Model_Plugin_QuoteItem();
    }

    /**
     * @dataProvider registryIdProvider
     * @param $registryId
     */
    public function testAroundItemToOrderUsualQuote($registryId)
    {
        $orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item',
            array('setGiftregistryItemId'), array(), '', false);
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $quoteItemMock = $this->getMock('Magento_Sales_Model_Quote_Item',
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
        $orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item',
            array('setGiftregistryItemId'), array(), '', false);
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($orderItemMock));

        $quoteItemMock = $this->getMock('Magento_Sales_Model_Quote_Address_Item',
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