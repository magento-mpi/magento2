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
 * @magentoDataFixture SalesOrder/_fixtures/order.php
 */
class SalesOrder_ListTest extends Magento_Test_Webservice
{
    /**
     * Test getting sales order list
     */
    public function testList()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('creditmemo/order');


        $filters = array(array(
            'filter' => array(
                array(
                    'key' => 'status',
                    'value' => array('eq' => $order->getData('status')),
                ),
                array(
                    'key' => 'created_at',
                    'value' => array('eq' => $order->getData('created_at')),
                )
            )
        ));

        $result = $this->getWebService()->call('order.list', $filters);

        //should be got array with one order item
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($order->getId(), $result[0]['order_id']);
    }

    /**
     * Delete created fixtures
     *
     * @static
     */
    static public function tearDownAfterClass()
    {
        self::deleteFixture('creditmemo/order', true);
        self::deleteFixture('creditmemo/product_virtual', true);
        self::deleteFixture('creditmemo/customer', true);
    }
}
