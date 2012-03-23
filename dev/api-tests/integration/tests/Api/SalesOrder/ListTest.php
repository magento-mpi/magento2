<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    }
}
