<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test API getting orders list method
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Api/Mage/SalesOrder/_fixture/order.php
 */
class SalesOrder_ListTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $filters = array(
            'filters' => (object)array(
                'filter' => array(
                    (object)array('key' => 'status', 'value' => $order->getData('status')),
                    (object)array('key' => 'created_at', 'value' => $order->getData('created_at'))
                ),
                'complex_filter' => array(
                    (object)array(
                        'key' => 'order_id',
                        'value' => (object)array('key' => 'in', 'value' => "{$order->getId()},0")
                    ),
                    array(
                        'key' => 'protect_code',
                        'value' => (object)array('key' => 'in', 'value' => $order->getData('protect_code'))
                    )
                )
            )
        );

        $result = Magento_Test_Helper_Api::call($this, 'salesOrderList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        //should be got array with one order item
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($order->getId(), $result[0]['order_id']);
    }
}
