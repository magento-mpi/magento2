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
class SalesOrder_StatusTest extends Magento_Test_Webservice
{
    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    public static function tearDownAfterClass()
    {
        Magento_Test_Webservice::deleteFixture('creditmemo/customer', true);
        Magento_Test_Webservice::deleteFixture('creditmemo/product_virtual', true);
        Magento_Test_Webservice::deleteFixture('creditmemo/order', true);

        parent::tearDownAfterClass();
    }

    /**
     * Test for sales_order.cancel
     *
     * @return void
     */
    public function testCancelPendingOrder()
    {
        // create new order in status 'pending'

        /** @var $order Mage_Sales_Model_Order */
        $order = Magento_Test_Webservice::getFixture('creditmemo/order');

        $soapResult = $this->getWebService()->call('sales_order.cancel', $order->getIncrementId());

        $this->assertTrue($soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_CANCELED, $order->getStatus(), 'Status is not CANCELED');

        // try to cancel order again
        $this->getWebService()->call('sales_order.cancel', $order->getIncrementId());
    }
}
