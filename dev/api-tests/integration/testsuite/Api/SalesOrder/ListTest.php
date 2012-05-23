<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API getting orders list method
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 * @magentoDataFixture Api/SalesOrder/_fixtures/order.php
 */
class Api_SalesOrder_ListTest extends Magento_Test_Webservice
{
    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

        if (TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2 || TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2_WSI) {
            $filters = array('filters' => array(
                'filter' => array(
                    array('key' => 'status', 'value' => $order->getData('status')),
                    array('key' => 'created_at', 'value' => $order->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key'   => 'order_id',
                        'value' => array('key' => 'in', 'value' => array($order->getId(), 0))
                    ),
                    array(
                        'key'   => 'protect_code',
                        'value' => array( 'key' => 'in', 'value' => array($order->getData('protect_code')))
                    )
                )
            ));
        } else {
            $filters = array(array(
                'status' => array('processing', $order->getData('status')),
                'created_at' => $order->getData('created_at'),
                'order_id' => array('in' => array($order->getId(), 0))
            ));
        }
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
