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

namespace Magento\Checkout\Test\TestCase\Guest;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Class OnepageTest
 * Test one page with PayPal Standard payment method
 *
 * @package Magento\Test\TestCase\Guest
 */
class PaypalStandardTest extends Functional
{
    /**
     * Guest checkout using PayPal Payments Standard method and offline shipping method
     *
     * @ZephyrId MAGETWO-12964
     */
    public function testOnepageCheckout()
    {
        /** @var \Magento\Sales\Test\Fixture\OrderCheckout $fixture */
        $fixture = Factory::getFixtureFactory()->getMagentoSalesPaypalStandardOrder();
        $fixture->persist();

        //Verify order in Backend
        $successPage = Factory::getPageFactory()->getCheckoutOnepageSuccess();
        $this->assertContains(
            'Your order has been received.',
            $successPage->getTitleBlock()->getTitle(),
            'Order success page was not opened.');

        $orderId = $fixture->getOrderId();
        Factory::getApp()->magentoBackendLoginUser();
        $orderPage = Factory::getPageFactory()->getSalesOrder();
        $orderPage->open();
        $orderPage->getOrderGridBlock()->searchAndOpen(array('id' => $orderId));
        $this->assertContains(
            $fixture->getCheckoutFixture()->getGrandTotal(),
            Factory::getPageFactory()->getSalesOrderView()->getOrderTotalsBlock()->getGrandTotal(),
            'Incorrect grand total value for the order #' . $orderId
        );
    }
}
