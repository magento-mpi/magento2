<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Bundle_Model_Plugin_QuoteItemTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Bundle_Model_Plugin_QuoteItem */
    protected $_model;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_quoteItemMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_invocationChainMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_orderItemMock;

    protected function setUp()
    {
        $this->_orderItemMock = $this->getMock('Magento_Sales_Model_Order_Item', array(), array(), '', false);
        $this->_quoteItemMock = $this->getMock('Magento_Sales_Model_Quote_Item', array(), array(), '', false);
        $this->_invocationChainMock = $this->getMock('Magento_Code_Plugin_InvocationChain',
            array(), array(), '', false);

        $this->_model = new Magento_Bundle_Model_Plugin_QuoteItem();
    }

    public function testAroundItemToOrderItemPositive()
    {
        $productMock = $this->getMock('Magento_Catalog_Model_Product', array(), array(), '', false);
        $bundleAttribute = $this->getMock('Magento_Catalog_Model_Product_Configuration_Item_Option',
            array(), array(), '', false);
        $productMock->expects($this->once())->method('getCustomOption')->with('bundle_selection_attributes')
            ->will($this->returnValue($bundleAttribute));
        $this->_quoteItemMock->expects($this->once())->method('getProduct')
            ->will($this->returnValue($productMock));
        $this->_orderItemMock->expects($this->once())->method('setProductOptions');
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);
    }

    public function testAroundItemToOrderItemNegative()
    {
        $productMock = $this->getMock('Magento_Catalog_Model_Product', array(), array(), '', false);
        $productMock->expects($this->once())->method('getCustomOption')->with('bundle_selection_attributes')
            ->will($this->returnValue(false));
        $this->_quoteItemMock->expects($this->once())->method('getProduct')
            ->will($this->returnValue($productMock));
        $this->_orderItemMock->expects($this->never())->method('setProductOptions');
        $this->_invocationChainMock->expects($this->once())->method('proceed')
            ->will($this->returnValue($this->_orderItemMock));

        $orderItem = $this->_model->aroundItemToOrderItem(array($this->_quoteItemMock), $this->_invocationChainMock);
        $this->assertSame($this->_orderItemMock, $orderItem);
    }
}
