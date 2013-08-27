<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Sales_Model_Quote_ItemTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Sales_Model_Quote_Item
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMock('Magento_Sales_Model_Quote_Item', null, array(), '', false);
    }

    public function testGetAddress()
    {
        $quote = $this->getMock('Magento_Sales_Model_Quote',
            array('getShippingAddress', 'getBillingAddress'), array(), '', false);
        $quote->expects($this->once())
            ->method('getShippingAddress')
            ->will($this->returnValue('shipping'));
        $quote->expects($this->once())
            ->method('getBillingAddress')
            ->will($this->returnValue('billing'));

        $this->_model->setQuote($quote);

        $quote->setItemsQty(2);
        $quote->setVirtualItemsQty(1);
        $this->assertEquals('shipping', $this->_model->getAddress(), 'Wrong shipping address');

        $quote->setItemsQty(2);
        $quote->setVirtualItemsQty(2);
        $this->assertEquals('billing', $this->_model->getAddress(), 'Wrong billing address');
    }
}
