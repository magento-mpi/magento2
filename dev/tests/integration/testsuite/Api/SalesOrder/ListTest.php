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
 * @magentoApiDataFixture Api/SalesOrder/_fixture/order.php
 */
class Api_SalesOrder_ListTest extends Magento_Test_TestCase_ApiAbstract
{
    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

        $filters = array(
            'filters' => array(
                'filter' => array(
                    array('key' => 'status', 'value' => $order->getData('status')),
                    array('key' => 'created_at', 'value' => $order->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key' => 'order_id',
                        'value' => array('key' => 'in', 'value' => "{$order->getId()},0")
                    ),
                    array(
                        'key' => 'protect_code',
                        'value' => array('key' => 'in', 'value' => $order->getData('protect_code'))
                    )
                )
            )
        );

        $result = $this->call('order.list', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        //should be got array with one order item
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($order->getId(), $result[0]['order_id']);
    }

    /**
     * Delete created fixtures
     */
    static public function tearDownAfterClass()
    {
        self::deleteFixture('order', true);
        self::deleteFixture('order2', true);
        self::deleteFixture('quote', true);
        self::deleteFixture('quote2', true);
        self::deleteFixture('product_virtual', true);
        self::deleteFixture('customer', true);
        parent::tearDownAfterClass();
    }
}
