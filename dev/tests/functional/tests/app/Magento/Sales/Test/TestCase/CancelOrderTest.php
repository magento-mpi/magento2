<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class CancelOrderTest
 * Test cancel order
 *
 * @package Magento\Sales\Test\TestCase\
 */
class CancelOrderTest extends Functional
{
    /**
     * Cancel order placed by PayPal Express from product page
     *
     * @dataProvider dataProviderOrder
     * @param string|int $orderId
     * @param string|int $grandTotal
     */
    public function testPayPalExpress($orderId, $grandTotal)
    {
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getAdminSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $grandTotal,
            Factory::getPageFactory()->getAdminSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
        //@TODO for MAGETWO-15505: Close this order here
    }

    /**
     * Data provider for testPayPalExpress
     *
     * @return array
     */
    public function dataProviderOrder()
    {
        $paypalExpressFixture = Factory::getFixtureFactory()->getMagentoCheckoutPaypalExpress();
        return array(
                array(
                    Factory::getApp()->magentoCheckoutCreatePaypalExpressOrder($paypalExpressFixture),
                    $paypalExpressFixture->getGrandTotal()
                )
        );
    }
}
