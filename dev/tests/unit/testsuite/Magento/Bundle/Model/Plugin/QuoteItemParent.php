<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Parent class for several quote item plugins
 */
class Magento_Bundle_Model_Plugin_QuoteItemParent extends PHPUnit_Framework_TestCase
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
    }
}
